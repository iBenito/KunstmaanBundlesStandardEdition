<?php

namespace Zizoo\CrewBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Zizoo\CrewBundle\Entity\SkillLicense;
use Zizoo\CrewBundle\Entity\Skills;
use Zizoo\CrewBundle\Form\SkillType;
use Zizoo\CrewBundle\Form\SkillsType;

/**
 * Skills controller.
 *
 */
class SkillsController extends Controller
{

    /**
     * Displays a form to create a new Skills entity.
     *
     */
    public function addAction()
    {
        $request = $this->getRequest();
        $entity = new Skills();
        $user = $this->getUser();
        $form   = $this->createForm(new SkillType(), $user);
        $em = $this->getDoctrine()->getManager();

        $routes = $request->query->get('routes');

        if ($request->isMethod('post')){
            $originalSkills = array();
            // Create an array of the current Tag objects in the database
            foreach ($user->getSkills() as $skill) {
                $originalSkills[] = $skill;
            }

            $form->bind($request);

            if ($form->isValid()) {

                // filter $originalTags to contain tags no longer present
                foreach ($user->getSkills() as $skill) {
                    foreach ($originalSkills as $key => $toDel) {
                        if ($toDel->getId() === $skill->getId()) {
                            unset($originalSkills[$key]);
                        }
                    }
                }

                // remove the relationship between the tag and the Task
                foreach ($originalSkills as $skill) {
                    // remove the Task from the Tag
                    $user->getSkills()->removeElement($skill);

                    // if it were a ManyToOne relationship, remove the relationship like this
                    // $tag->setTask(null);

                    //$em->persist($skill);

                    // if you wanted to delete the Tag entirely, you can also do that
                    $em->remove($skill);
                }

                $em->persist($user);
                $em->flush();

                $this->redirect($this->generateUrl($routes['skills_route']));

            }

        }

        return $this->render('ZizooCrewBundle:Skills:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'url'    => $routes['skills_route']
        ));
    }

    private function setSkillLicense(Skills $skill, $licenseFile, $validationGroup='license', $flush=true)
    {
        if (!$licenseFile instanceof UploadedFile){
            throw new \Exception('Unable to upload');
        }
        $em = $this->getDoctrine()->getManager();

        $oldLicense = $skill->getLicense();

        $license = new SkillLicense();
        $license->setFile($licenseFile);
        $license->setPath($licenseFile->guessExtension());
        $license->setOriginalFilename($licenseFile->getClientOriginalName());
        $license->setMimeType($licenseFile->getMimeType());

        $validator          = $this->container->get('validator');
        $licenseErrors      = $validator->validate($license, $validationGroup);
        $numLicenseErrors   = $licenseErrors->count();

        if ($numLicenseErrors==0){

            $license->setSkill($skill);
            $skill->setLicense($license);
            $skill->setUpdated(new \DateTime());

            try {
                $em->persist($license);

                // Remove old logo
                if ($oldLicense) $em->remove($oldLicense);

                if ($flush) $em->flush();

                return $license;

            } catch (\Exception $e){
                throw new \Exception('Unable to upload: ' . $e->getMessage());
            }

        } else {

            $errorArr = array();
            for ($i=0; $i<$numLicenseErrors; $i++){
                $error = $licenseErrors->get($i);
                $msgTemplate = $error->getMessage();
                $errorArr[] = $msgTemplate;
            }

            throw new \Exception(join(',', $errorArr));

        }
    }

    /**
     * Set License to Skill entity.
     *
     */
    public function setLicenseAction()
    {
        $request    = $this->getRequest();
        $user       = $this->getUser();
        $em         = $this->getDoctrine()->getManager();

        $validationGroup = 'license';
        $licenseFile        = $request->files->get('licenseFile');
        $skillId            = $request->request->get('skill_id', null);
        $skill = $em->getRepository('ZizooCrewBundle:Skills')->findOneById($skillId);
        if ($skill===null){
            $skill = new Skills();
            $skill->setUser($user);
            $user->addSkill($skill);
            $em->persist($skill);
            $em->flush();
            $validationGroup = 'skill_new_license';
        }

        try {
            $license = $this->setSkillLicense($skill, $licenseFile, $validationGroup, true);
            return new JsonResponse(array('message' => 'Your license has been uploaded successfully', 'license_id' => $license->getId(), 'skill_id' => $license->getSkill()->getId()));
        } catch (\Exception $e){
            return new Response($e->getMessage(), 400);
        }

    }

    public function getLicenseAction()
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirect($this->generateUrl('ZizooBaseBundle_homepage'));
        }

        $form   = $this->createForm(new SkillType(), $user);
        return $this->render('ZizooCrewBundle:Skills:license.html.twig',array(
            'form' => $form->createView()
        ));
    }

}
