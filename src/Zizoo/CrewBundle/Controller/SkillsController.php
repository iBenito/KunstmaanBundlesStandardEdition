<?php

namespace Zizoo\CrewBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Zizoo\CrewBundle\Entity\Skills;
use Zizoo\CrewBundle\Form\SkillsType;

/**
 * Skills controller.
 *
 */
class SkillsController extends Controller
{
    /**
     * Lists all Skills entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ZizooCrewBundle:Skills')->findAll();

        return $this->render('ZizooCrewBundle:Skills:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Skills entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZizooCrewBundle:Skills')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Skills entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ZizooCrewBundle:Skills:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Skills entity.
     *
     */
    public function addAction()
    {
        $entity = new Skills();
        $form   = $this->createForm(new SkillsType(), $entity);

        return $this->render('ZizooCrewBundle:Skills:add.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Skills entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Skills();
        $form = $this->createForm(new SkillsType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('skills_show', array('id' => $entity->getId())));
        }

        return $this->render('ZizooCrewBundle:Skills:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Skills entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZizooCrewBundle:Skills')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Skills entity.');
        }

        $editForm = $this->createForm(new SkillsType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ZizooCrewBundle:Skills:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Skills entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ZizooCrewBundle:Skills')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Skills entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new SkillsType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('skills_edit', array('id' => $id)));
        }

        return $this->render('ZizooCrewBundle:Skills:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Skills entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ZizooCrewBundle:Skills')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Skills entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('skills'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
