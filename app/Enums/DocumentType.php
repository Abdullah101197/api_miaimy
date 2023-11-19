<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class DocumentType extends Enum
{
    const Matriculation = 'Matriculation Mark sheet & Certificate';
    const Intermediate = 'Intermediate Mark sheet & Certificate';
    const OLevels = 'O Levels';
    const ALevels = 'A Levels Certificate';
    const BachelDegreeTranscript = 'Bachelor Degree & Transcript';
    const MasterDegreeTranscript = 'Master Degree & Transcript';
    const StatementOfPurpose = 'Statement of Purpose – 700 to 800 words (Important for Admissions)';
    const ReferenceLetters = 'Two Reference Letters';
    const RecommendationLetters = 'Two Recommendation Letters';
    const ExperienceCertificate = 'Experience Certificate(If any)';
    const EmployerReference = 'Employer Reference (If any)';
    const IELTSTOEFLPTE = 'IELTS/TOEFL/PTE';
    const CurriculumVitae = 'Curriculum Vitae';
    const PassportScan = 'Passport Scan (First 2 Pages)';
    const AccountMaintenanceLetterBankStatement = 'Account Maintenance Letter and Bank Statement';
    const UKTBMedicalTest = 'UKTB (Medical Test)';
}
