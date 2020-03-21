<?php

declare(strict_types=1);

namespace Pehapkari\Fakturoid\Factory;

use Defr\Ares;
use Defr\Ares\AresException;
use Pehapkari\Exception\ShouldNotHappenException;
use Pehapkari\Registration\Entity\TrainingRegistration;

final class SubjectDataFactory
{
    private Ares $ares;

    public function __construct(Ares $ares)
    {
        $this->ares = $ares;
    }

    /**
     * @return mixed[]
     */
    public function createFromTrainingRegistration(TrainingRegistration $trainingRegistration): array
    {
        $data = [
            'name' => $this->createName($trainingRegistration),
        ];

        if ($trainingRegistration->getEmail() !== null) {
            $data['email'] = $trainingRegistration->getEmail();
        }

        if ($trainingRegistration->getPhone() !== null) {
            $data['phone'] = $trainingRegistration->getPhone();
        }

        if (is_numeric($trainingRegistration->getIco())) { // probably ICO
            $data['registration_no'] = $trainingRegistration->getIco();

            $aresRecord = $this->ares->findByIdentificationNumber($trainingRegistration->getIco());

            $data['street'] = $aresRecord->getStreetWithNumbers();
            $data['city'] = $aresRecord->getTown();
            $data['zip'] = $aresRecord->getZip();

            if ($aresRecord->getTaxId()) {
                $data['vat_no'] = $aresRecord->getTaxId();
            }
        }

        return $data;
    }

    private function createName(TrainingRegistration $trainingRegistration): string
    {
        if (is_numeric($trainingRegistration->getIco())) { // probably ICO
            try {
                $aresRecord = $this->ares->findByIdentificationNumber($trainingRegistration->getIco());
            } catch (AresException $aresException) {
                throw new ShouldNotHappenException(sprintf(
                    'Ares lookup failed for ID "%s": "%s"',
                    $trainingRegistration->getIco(),
                    $aresException->getMessage()
                ), $aresException->getCode(), $aresException);
            }

            // prefer company name in ARES
            return $aresRecord->getCompanyName();
        }

        return (string) $trainingRegistration->getName();
    }
}
