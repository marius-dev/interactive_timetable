<?php


namespace AppBundle\Service;


use AppBundle\Model\NodeEntity\Department;
use AppBundle\Model\NodeEntity\Series;
use AppBundle\Model\NodeEntity\Specialization;
use AppBundle\Service\Traits\EntityManagerTrait;
use AppBundle\Service\Traits\TranslatorTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class SpecializationManagerService
 * @package AppBundle\Service
 */
class SpecializationManagerService
{
    use EntityManagerTrait;
    use TranslatorTrait;

    const SERVICE_NAME = 'app.specialization_manager.service';

    /**
     * @param string $shortName
     * @param string $fullName
     * @param Department $department
     * @param Series[] | null $series
     * @return Specialization
     */
    public function createNew(string $shortName, string $fullName, Department $department, $series = null)
    {
        $result = $this->getEntityManager()
            ->getRepository(Specialization::class)
            ->findOneBy(
                array(
                    'name' => $shortName,
                    'department' => $department->getId()
                )
            );

        if ($result != null) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                $this->getTranslator()->trans('app.warnings.specialization.already_exists')
            );
        }

        $specialization = new Specialization($shortName, $fullName, $department);


        if ($series != null) {
            $specialization->setSeries($series);
        }


        $this->getEntityManager()->persist($specialization);
        $this->getEntityManager()->flush();

        return $specialization;
    }

    /**
     * @param Specialization $specialization
     * @param Series $series
     */
    public function addSeries(Specialization $specialization, Series $series)
    {
        $series->setSpecialization($specialization);

        if ($specialization->getSeries()->contains($series)) {
            throw new HttpException(
                Response::HTTP_CONFLICT,
                $this->getTranslator()->trans('app.warnings.series.already_exists') . ' ' . $series->getName()
            );
        }

        $specialization->getSeries()->add($series);

        $this->getEntityManager()->persist($series);
        $this->getEntityManager()->persist($specialization);
        $this->getEntityManager()->flush();
    }

    /**
     * @param Specialization $specialization
     * @param Series $series
     */
    public function removeSeries(Specialization $specialization, Series $series)
    {
        if (!$specialization->getSeries()->removeElement($series)) {
            throw new HttpException(
                Response::HTTP_NOT_FOUND,
                $this->getTranslator()->trans('app.warnings.series.does_not_exists')
            );
        }

        $this->getEntityManager()->persist($series);
        $this->getEntityManager()->persist($specialization);
        $this->getEntityManager()->flush();
    }


    /**
     * @param int $specializationId
     */
    public function removeSpecializationById(int $specializationId)
    {
        $specialization = $this->getSpecializationById($specializationId);

        $this->getEntityManager()->remove($specialization);
        $this->getEntityManager()->flush();
    }

    /**
     * @param int $specializationId
     * @return Specialization| null
     */
    public function getSpecializationById(int $specializationId)
    {
        /** @var Specialization $specialization */
        $specialization = $this->getEntityManager()
            ->getRepository('AppBundle\Model\NodeEntity\Specialization')
            ->findOneById($specializationId);

        if ($specialization == null) {
            throw new HttpException(
                Response::HTTP_NOT_FOUND,
                $this->getTranslator()->trans('app.warnings.specialization.does_not_exists')
            );
        }

        return $specialization;
    }
}