<?php

namespace MediaBundle\Controller;

use MediaBundle\Entity\Album;
use MediaBundle\Entity\Commentaire;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use MediaBundle\Form\CommentaireType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use MediaBundle\Form\AlbumType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Album controller.
 *
 */
class AlbumController extends Controller
{
    /**
     * Lists all album entities.
     *
     */
    public function indexAction($genre = "All", Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if($genre == "All") {
            $albums = $em->getRepository('MediaBundle:Album')->findAll();
        } else {
            $albums = $em->getRepository('MediaBundle:Album')->findBy(array('genre'=>$genre));
        };

        $albumc = new Album();
        $form = $this->createForm('MediaBundle\Form\AlbumchooseType', $albumc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($albumc);
            return $this->redirectToRoute('_index', array('genre' => $albumc->getGenre()));
        }

        return $this->render('album/index.html.twig', array(
            'albums' => $albums,
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new album entity.
     *
     */
    public function newAction(Request $request)
    {
        $album = new Album();
        $form = $this->createForm('MediaBundle\Form\AlbumType', $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /* Ajout recup et gestion file */
            $file = $album->getImg();
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('brochures_directory'),
                $fileName
            );
            $album->setImg($fileName);

            /* Reprise code standard */
            $em = $this->getDoctrine()->getManager();
            $em->persist($album);
            $em->flush($album);

            return $this->redirectToRoute('_show', array('id' => $album->getId()));
        }

        return $this->render('album/new.html.twig', array(
            'album' => $album,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a album entity.
     *
     */
    public function showAction(Album $album, Request $request)
    {
        $deleteForm = $this->createDeleteForm($album);
        $comment = $album->getCommentaire();

        $commentaire = new Commentaire();
        $form = $this->createForm('MediaBundle\Form\CommentaireType', $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commentaire);
            $em->flush($commentaire);
        }


        return $this->render('album/show.html.twig', array(
            'album' => $album,
            'comment' => $comment,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing album entity.
     *
     */
    public function editAction(Request $request, Album $album)
    {
        $deleteForm = $this->createDeleteForm($album);
        $editForm = $this->createForm('MediaBundle\Form\AlbumType', $album);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('_edit', array('id' => $album->getId()));
        }

        return $this->render('album/edit.html.twig', array(
            'album' => $album,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a album entity.
     *
     */
    public function deleteAction(Request $request, Album $album)
    {
        $form = $this->createDeleteForm($album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($album);
            $em->flush($album);
        }

        return $this->redirectToRoute('_index');
    }

    /**
     * Creates a form to delete a album entity.
     *
     * @param Album $album The album entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Album $album)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('_delete', array('id' => $album->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
