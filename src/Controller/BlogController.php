<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\BlogCategory;
use App\Entity\Subscribers;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class BlogController extends GlobalController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(EntityManagerInterface $entityManagerInterface, PaginatorInterface $paginator, Request $request): Response
    {
        $dbh = $entityManagerInterface->getConnection();
        $stmt = $dbh->prepare("SELECT c.formatter_caegory_name AS category_name, COUNT(b.id) AS blog_count FROM blog_category c LEFT JOIN blog b ON c.id = b.category_id GROUP BY c.id");
        $categories = $stmt->executeQuery()->fetchAll();
        $repo = $entityManagerInterface->getRepository(Blog::class);
        $query = $repo->createQueryBuilder('blog');
        $query->orderBy('blog.id', 'DESC');
        if ($request->query->get('type')) {
            $query->andWhere('blog.category_name = :categoryName')
                ->setParameter('categoryName', $request->query->get('type'));
        }
        $query->getQuery();
        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            4 /* items per page */
        );
        return $this->render('frontend/blog/index.html.twig', [
            'page_title' => 'Blog',
            'pagination' => $pagination,
            'categories' => $categories,
        ]);
    }

    #[Route('/blog/{blog_slug}', name: 'app_blog_single')]
    public function singlBlog($blog_slug, EntityManagerInterface $entityManagerInterface): Response
    {
        $blogData = $entityManagerInterface->getRepository(Blog::class)->findOneBy(['user_id' => 1, 'slug' => $blog_slug]);
        return $this->render('frontend/blog/blog_single.html.twig', [
            'page_title' => $blog_slug,
            'blog' => $blogData,
        ]);
    }

    #[Route('/administrator/blog-list', name: 'app_blog_list')]
    public function blogList(EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $getBlogs = $entityManagerInterface->getRepository(Blog::class)->findBy(['user_id' => $this->getUser()->getId()]);
        return $this->render('backend/blog/blog_list.html.twig', [
            'blogs' => $getBlogs,
        ]);
    }
    #[Route('/administrator/add-blog', name: 'app_blog_add')]
    public function addBlog(EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $blogCategories = $entityManagerInterface->getRepository(BlogCategory::class)->findBy(['user_id' => $this->getUser()->getId()]);
        if (!$request->query->get('blog_id')) {
            return $this->render('backend/blog/add_blog.html.twig', [
                "blogCategories" => $blogCategories,
            ]);
        } else {
            $blogDetails = $entityManagerInterface->getRepository(Blog::class)->findOneBy(['user_id' => $this->getUser()->getId(), 'id' => $request->query->get('blog_id')]);
            return $this->render('backend/blog/add_blog.html.twig', [
                "blogCategories" => $blogCategories,
                'blog' => $blogDetails,
            ]);
        }
    }
    #[Route('/administrator/save-blog', name: 'app_blog_save')]
    public function saveBlog(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $inputs = $request->request->all();
        if ($inputs) {
            $checkCategory = $entityManagerInterface->getRepository(BlogCategory::class);
            $hasCategory = $checkCategory->findOneBy(['user_id' => $this->getUser()->getId(), 'category_name' => strtolower($inputs['category'])]);
            $category_id = '';
            $category_name = $inputs['category'];
            if ($hasCategory) {
                $category_id = $hasCategory->getId();
                $category_name = $hasCategory->getFormatterCaegoryName();
            } else {
                $category = new BlogCategory;
                $category->setUserId($this->getUser()->getId());
                $category->setCategoryName(strtolower($inputs['category']));
                $category->setFormatterCaegoryName($inputs['category']);
                $entityManagerInterface->persist($category);
                $entityManagerInterface->flush();
                $category_id = $category->getId();
            }
            $file = $request->files->get('image');
            $fileId = $existingFileName = '';
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                if ($file instanceof UploadedFile) {
                    // Ensure it's a valid file
                    $content = file_get_contents($file->getPathname());
                    $mimeType = $file->getMimeType();
                    $fileId = $this->uplaodFileToDrive($content, $mimeType, $fileName, $existingFileName);
                }
            }
            if ($inputs['blog_number'] != '') {
                $blog = $entityManagerInterface->getRepository(Blog::class)->findOneBy(['id' => $inputs['blog_number']]);
                $blog->setUserId($this->getUser()->getId());
                $blog->setCategoryId($category_id);
                $blog->setCategoryName($category_name);
                $blog->setBlogHeading($inputs['heading']);
                $blog->setBlogContent($inputs['description']);
                $blog->setShortDescription($inputs['short_description']);
                $blog->setDate(date("j M Y"));
                $blog->setSlug($inputs['slug']);
                if ($fileId != '')
                    $blog->setImage($fileId);
                $entityManagerInterface->persist($blog);
                $entityManagerInterface->flush();
            } else {
                $blog = new Blog;
                $blog->setUserId($this->getUser()->getId());
                $blog->setCategoryId($category_id);
                $blog->setCategoryName($category_name);
                $blog->setBlogHeading($inputs['heading']);
                $blog->setBlogContent($inputs['description']);
                $blog->setShortDescription($inputs['short_description']);
                $blog->setDate(date("j M Y"));
                $blog->setSlug($inputs['slug']);
                if ($fileId != '')
                    $blog->setImage($fileId);
                $entityManagerInterface->persist($blog);
                $entityManagerInterface->flush();
            }
        }
        return $this->redirectToRoute('app_blog_list');
    }

    #[Route('/administrator/delete-blog', name: 'app_blog_delete')]
    public function deleteBlog(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        if ($request->query->get('blog_id')) {
            $entity = $entityManagerInterface->getRepository(Blog::class)->findOneBy(['id' => $request->query->get('blog_id')]);
            $entityManagerInterface->remove($entity);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('app_blog_list');
        }
    }

    #[Route('/save-subscriber', name: 'app_blog_subscriber', methods: 'POST')]
    public function saveSubscriber(Request $request, EntityManagerInterface $entityManagerInterface, MailerInterface $mailerInterface): Response
    {
        $email = $request->request->get('email');
        $saveData = new Subscribers;
        $saveData->setEmail($email);
        $entityManagerInterface->persist($saveData);
        $entityManagerInterface->flush();

        /* sending mail */
        $email = (new TemplatedEmail())
            ->from('soumyamanna180898@gmail.com')
            ->to($email)
            ->subject("Thank's for SUBSCRIBING")
            ->htmlTemplate('frontend\mail_templates\subscribe_template.html.twig') // Template path
            ->context([]); // variables wants to pass

        $mailerInterface->send($email);
        return new Response('hi');
    }
}
