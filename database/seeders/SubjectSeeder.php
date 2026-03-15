<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $programs = Program::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'program_name']);

        if ($programs->isEmpty()) {
            $programs = Program::query()->orderBy('id')->get(['id', 'program_name']);
        }

        if ($programs->isEmpty()) {
            return;
        }

        $subjectMap = [
            'BS Information Technology' => [
                ['subject_code' => 'PROG101', 'subject_name' => 'Programming Fundamentals', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'DB301', 'subject_name' => 'Database Systems', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'WD401', 'subject_name' => 'Web Development', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'NET301', 'subject_name' => 'Computer Networks', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'CYB401', 'subject_name' => 'Cybersecurity Fundamentals', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Computer Science' => [
                ['subject_code' => 'CAL101', 'subject_name' => 'Calculus 1', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'CAL102', 'subject_name' => 'Calculus 2', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'DSA201', 'subject_name' => 'Data Structures and Algorithms', 'units' => 4, 'semester' => 'Second'],
                ['subject_code' => 'OS301', 'subject_name' => 'Operating Systems', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'AI401', 'subject_name' => 'Artificial Intelligence', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Information Systems' => [
                ['subject_code' => 'IS201', 'subject_name' => 'Systems Analysis and Design', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'IS202', 'subject_name' => 'Enterprise Systems', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'IS301', 'subject_name' => 'Business Intelligence', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'IS302', 'subject_name' => 'Information Governance', 'units' => 2, 'semester' => 'Second'],
                ['subject_code' => 'IS401', 'subject_name' => 'Digital Transformation Strategy', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Data Science' => [
                ['subject_code' => 'STAT201', 'subject_name' => 'Statistics for Computing', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'DS202', 'subject_name' => 'Data Wrangling Techniques', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'DS301', 'subject_name' => 'Predictive Analytics', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'DS302', 'subject_name' => 'Machine Learning Foundations', 'units' => 4, 'semester' => 'Second'],
                ['subject_code' => 'DS401', 'subject_name' => 'Big Data Processing', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Software Engineering' => [
                ['subject_code' => 'PROG201', 'subject_name' => 'Object Oriented Programming', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'SE301', 'subject_name' => 'Requirements Engineering', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'SE302', 'subject_name' => 'Software Quality Assurance', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'SE401', 'subject_name' => 'Software Engineering', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'SE402', 'subject_name' => 'DevOps and Release Management', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Cybersecurity' => [
                ['subject_code' => 'CYB201', 'subject_name' => 'Network Defense', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'CYB202', 'subject_name' => 'Ethical Hacking', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'CYB301', 'subject_name' => 'Security Operations', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'CYB302', 'subject_name' => 'Digital Forensics', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'CYB402', 'subject_name' => 'Cloud Security', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Computer Engineering' => [
                ['subject_code' => 'ARCH301', 'subject_name' => 'Computer Architecture', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'CPE201', 'subject_name' => 'Digital Logic Design', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'CPE202', 'subject_name' => 'Microprocessors', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'CPE301', 'subject_name' => 'Embedded Systems', 'units' => 4, 'semester' => 'Second'],
                ['subject_code' => 'CPE401', 'subject_name' => 'VLSI Fundamentals', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Civil Engineering' => [
                ['subject_code' => 'CE101', 'subject_name' => 'Engineering Mechanics', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'CE201', 'subject_name' => 'Surveying', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'CE202', 'subject_name' => 'Strength of Materials', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'CE301', 'subject_name' => 'Geotechnical Engineering', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'CE401', 'subject_name' => 'Transportation Engineering', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Mechanical Engineering' => [
                ['subject_code' => 'ME101', 'subject_name' => 'Thermodynamics 1', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'ME201', 'subject_name' => 'Fluid Mechanics', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'ME202', 'subject_name' => 'Machine Design', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'ME301', 'subject_name' => 'Computer Aided Design', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'ME401', 'subject_name' => 'Industrial Robotics', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Electrical Engineering' => [
                ['subject_code' => 'EE101', 'subject_name' => 'Circuit Analysis', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'EE201', 'subject_name' => 'Electromagnetics', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'EE202', 'subject_name' => 'Power Systems', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'EE301', 'subject_name' => 'Control Systems', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'EE401', 'subject_name' => 'Renewable Energy Systems', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Industrial Engineering' => [
                ['subject_code' => 'IE101', 'subject_name' => 'Work Study and Ergonomics', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'IE201', 'subject_name' => 'Operations Research', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'IE202', 'subject_name' => 'Quality Management', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'IE301', 'subject_name' => 'Supply Chain Engineering', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'IE401', 'subject_name' => 'Lean Manufacturing', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Accountancy' => [
                ['subject_code' => 'ACC101', 'subject_name' => 'Financial Accounting', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'ACC201', 'subject_name' => 'Managerial Accounting', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'ACC202', 'subject_name' => 'Cost Accounting', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'ACC301', 'subject_name' => 'Taxation and Compliance', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'ACC401', 'subject_name' => 'Auditing Theory', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Business Administration' => [
                ['subject_code' => 'BA101', 'subject_name' => 'Principles of Management', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'BA201', 'subject_name' => 'Business Finance', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'BA202', 'subject_name' => 'Human Resource Management', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'BA301', 'subject_name' => 'Operations Management', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'BA401', 'subject_name' => 'Strategic Management', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Marketing Management' => [
                ['subject_code' => 'MKT201', 'subject_name' => 'Consumer Behavior', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'MKT202', 'subject_name' => 'Brand Management', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'MKT301', 'subject_name' => 'Marketing Analytics', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'MKT302', 'subject_name' => 'Digital Marketing Strategy', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'MKT401', 'subject_name' => 'Integrated Marketing Communications', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Financial Management' => [
                ['subject_code' => 'FIN101', 'subject_name' => 'Fundamentals of Finance', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'FIN201', 'subject_name' => 'Investment Analysis', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'FIN202', 'subject_name' => 'Corporate Finance', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'FIN301', 'subject_name' => 'Risk Management', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'FIN401', 'subject_name' => 'Banking and Treasury', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Hospitality Management' => [
                ['subject_code' => 'HM101', 'subject_name' => 'Hospitality Operations', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'HM201', 'subject_name' => 'Food and Beverage Management', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'HM202', 'subject_name' => 'Front Office Administration', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'HM301', 'subject_name' => 'Hotel Revenue Management', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'HM401', 'subject_name' => 'Events and Convention Planning', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Tourism Management' => [
                ['subject_code' => 'TM101', 'subject_name' => 'Introduction to Tourism', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'TM201', 'subject_name' => 'Tourism Product Development', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'TM202', 'subject_name' => 'Destination Planning', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'TM301', 'subject_name' => 'Travel Operations Management', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'TM401', 'subject_name' => 'Cultural Heritage Interpretation', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Biology' => [
                ['subject_code' => 'BIO101', 'subject_name' => 'General Biology 1', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'BIO201', 'subject_name' => 'Cell and Molecular Biology', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'BIO202', 'subject_name' => 'Genetics', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'BIO301', 'subject_name' => 'Ecology', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'BIO401', 'subject_name' => 'Biotechnology Applications', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Chemistry' => [
                ['subject_code' => 'CHEM101', 'subject_name' => 'General Chemistry', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'CHEM201', 'subject_name' => 'Organic Chemistry', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'CHEM202', 'subject_name' => 'Inorganic Chemistry', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'CHEM301', 'subject_name' => 'Analytical Chemistry', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'CHEM401', 'subject_name' => 'Physical Chemistry', 'units' => 3, 'semester' => 'Second'],
            ],
            'BS Physics' => [
                ['subject_code' => 'PHY101', 'subject_name' => 'Physics 1', 'units' => 3, 'semester' => 'First'],
                ['subject_code' => 'PHY102', 'subject_name' => 'Physics 2', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'PHY301', 'subject_name' => 'Quantum Mechanics', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'PHY302', 'subject_name' => 'Thermal Physics', 'units' => 3, 'semester' => 'Second'],
                ['subject_code' => 'PHY401', 'subject_name' => 'Optics and Photonics', 'units' => 3, 'semester' => 'Second'],
            ],
        ];

        $allCodes = collect($subjectMap)
            ->flatten(1)
            ->pluck('subject_code');

        if ($allCodes->count() !== $allCodes->unique()->count()) {
            throw new \RuntimeException('Duplicate subject_code found in SubjectSeeder map.');
        }

        $keptIds = [];

        foreach ($programs as $program) {
            $subjects = $subjectMap[$program->program_name] ?? [];

            foreach ($subjects as $subject) {
                $record = Subject::query()->updateOrCreate(
                    ['subject_code' => $subject['subject_code']],
                    [
                        'subject_name' => $subject['subject_name'],
                        'program_id' => $program->id,
                        'units' => $subject['units'],
                        'semester' => $subject['semester'],
                        'prerequisite_id' => null,
                    ]
                );

                $keptIds[] = $record->id;
            }
        }

        $uniqueKeptIds = collect($keptIds)->unique()->values();

        if ($uniqueKeptIds->isNotEmpty()) {
            Subject::query()->whereNotIn('id', $uniqueKeptIds)->delete();
        }

        $count = Subject::query()->count();

        if ($count < 60 || $count > 120) {
            throw new \RuntimeException('SubjectSeeder must generate between 60 and 120 records.');
        }
    }
}
