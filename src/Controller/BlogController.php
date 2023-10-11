<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\BlogCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(): Response
    {
        return $this->render('frontend/blog/index.html.twig', [
            'page_title' => 'Blog',
        ]);
    }

    #[Route('/blog/{blog_slug}', name: 'app_blog_single')]
    public function singlBlog($blog_slug): Response
    {
        return $this->render('frontend/blog/blog_single.html.twig', [
            'page_title' => $blog_slug,
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
            if ($inputs['blog_number'] != '') {
                $blog = $entityManagerInterface->getRepository(Blog::class)->findOneBy(['id' => $inputs['blog_number']]);
                $blog->setUserId($this->getUser()->getId());
                $blog->setCategoryId($category_id);
                $blog->setCategoryName($category_name);
                $blog->setBlogHeading($inputs['heading']);
                $blog->setBlogContent($inputs['description']);
                $blog->setDate(date('d/m/Y'));
                $blog->setSlug($inputs['slug']);
                $entityManagerInterface->persist($blog);
                $entityManagerInterface->flush();
            } else {
                $blog = new Blog;
                $blog->setUserId($this->getUser()->getId());
                $blog->setCategoryId($category_id);
                $blog->setCategoryName($category_name);
                $blog->setBlogHeading($inputs['heading']);
                $blog->setBlogContent($inputs['description']);
                $blog->setDate(date('d/m/Y'));
                $blog->setSlug($inputs['slug']);
                $entityManagerInterface->persist($blog);
                $entityManagerInterface->flush();
            }
        }
        return $this->redirectToRoute('app_blog_list');
    }

    #[Route('/administrator/delete-blog', name: 'app_blog_delete')]
    public function deleteBlog(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        if ($request->query->get('blog_id')) {
            $entity = $entityManagerInterface->getRepository(Blog::class)->findOneBy(['id' => $request->query->get('blog_id')]);
            $entityManagerInterface->remove($entity);
            $entityManagerInterface->flush();
            return $this->redirectToRoute('app_blog_list');
        }
    }
}
