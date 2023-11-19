<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\AdminUploadedDocument;
use App\Models\UserDocument;
use App\Repositories\AdmissionApplicationRepository;
use App\Models\AdmissionApplication;
use App\Models\Notification as ModelsNotification;
use App\Models\User;
use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\MissingDocumentsNotification; // Import the notification class
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use stdClass;

final class AdmissionApplicationService

{

    protected $AdmissionApplicationRepository;

    public function __construct(AdmissionApplicationRepository $AdmissionApplicationRepository)
    {
        $this->AdmissionApplicationRepository = $AdmissionApplicationRepository;
    }

    function applyForAdmission($data)
    {
        $requiredDocuments = $data['requiredDocuments'];
        $requiredDocuments = trim($requiredDocuments, '[]');
        $requiredDocuments = collect(explode(",", $requiredDocuments))
            ->map(function ($element) {
                return trim($element, "'\"");
            })
            ->toArray();
        $userdoc = UserDocument::where('user_id', $data['user_id'])->pluck('doc_name')->toArray();

        if (empty($userdoc)) {
            return response()->json(['message' => 'User ID not found. No documents associated with this user']);
        } else {
            $docNames = array_unique($userdoc);
            $prefix = 'APP';
            $randomPart = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $timestampPart = now()->format('YmdHis');

            $applicationNumber = $prefix . $timestampPart . $randomPart;

            while (AdmissionApplication::where('Application_no', $applicationNumber)->exists()) {
                $randomPart = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT); // Generate a new random 4-digit number
                $applicationNumber = $prefix . $timestampPart . $randomPart;
            }
            $missingDocuments = array_diff($requiredDocuments, $docNames);
            if (empty($data['status'])) {
                $status = empty($missingDocuments) ? '1' : '0';
            } else {
                $status = $data['status'];
            }
            $requiredDocumentsData = [];
            foreach ($requiredDocuments as $document) {
                try {
                    $labelvalue = DocumentType::__callStatic($document, []);
                    $documentData = new stdClass();
                    $documentData->value = $document;
                    $documentData->label = $labelvalue->value;
                    $requiredDocumentsData[] = $documentData;
                } catch (InvalidEnumKeyException $exception) {
                    $documentData = new stdClass();
                    $documentData->value = $document;
                    $documentData->label = null;
                    $requiredDocumentsData[] = $documentData;
                }
            }

            $outputString = json_encode($requiredDocumentsData);
            $missingDocumentdata = json_encode($missingDocuments);

            $name = User::where('id', $data['user_id'])->get()->value('name');
            $applicationData = [
                'application_no' => $applicationNumber,
                'course_name' => $data['course_name'],
                'applied_university' => $data['applied_university'],
                'user_id' => $data['user_id'],
                'status' => $status,
                'missing_documents' => $missingDocumentdata,
                'application_date' => now(),
                'user_id' => $data['user_id'],
                'required_doc' => $outputString,

            ];
            $application = AdmissionApplication::create($applicationData);
            $applicantID  = $data['user_id'];
            $applicantName = User::where('id', $applicantID)->get()->value('name');
            $Result = $this->AdmissionApplicationRepository->Notification($applicantID, $missingDocuments);

            if ($status === '0') {
                $this->sendAlertNotification($applicantName, $applicantID, $missingDocuments);
                return response()->json(['message' => 'Application is Pending due to missing documents']);
            } else {
                $missingDocuments = 'empty';
                $this->sendAlertNotification($applicantName, $applicantID, $missingDocuments);
                return response()->json(['message' => 'Application submitted successfully']);
            }
        }
    }


    function updateApplyForAdmission($data)
    {
        $requiredDocuments = $data['requiredDocuments'];
        $requiredDocuments = trim($requiredDocuments, '[]');
        $requiredDocuments = collect(explode(",", $requiredDocuments))
            ->map(function ($element) {
                return trim($element, "'\"");
            })
            ->toArray();
        $userdoc = UserDocument::where('user_id', $data['user_id'])->pluck('doc_name')->toArray();

        if (empty($userdoc)) {
            return response()->json(['message' => 'User ID not found. No documents associated with this user']);
        } else {
            $docNames = array_unique($userdoc);
            $missingDocuments = array_diff($requiredDocuments, $docNames);
            if (empty($data['status'])) {
                $status = empty($missingDocuments) ? '1' : '0';
            } else {
                $status = $data['status'];
            }
            $requiredDocumentsData = [];
            foreach ($requiredDocuments as $document) {
                try {
                    $labelvalue = DocumentType::__callStatic($document, []);
                    $documentData = new stdClass();
                    $documentData->value = $document;
                    $documentData->label = $labelvalue->value;
                    $requiredDocumentsData[] = $documentData;
                } catch (InvalidEnumKeyException $exception) {
                    $documentData = new stdClass();
                    $documentData->value = $document;
                    $documentData->label = null;
                    $requiredDocumentsData[] = $documentData;
                }
            }

            $outputString = json_encode($requiredDocumentsData);
            $missingDocumentdata = json_encode($missingDocuments);

            $existingApplication = AdmissionApplication::where('Application_no', $data['Application_no'])->first();

            if (!$existingApplication) {
                return response()->json(['message' => 'Application not found']);
            }

            $existingApplication->course_name = $data['course_name'];
            $existingApplication->applied_university = $data['applied_university'];
            $existingApplication->status = $status;
            $existingApplication->missing_documents = $missingDocumentdata;
            $existingApplication->required_doc = $outputString;
            $existingApplication->application_date = now();
            $existingApplication->save();

            return response()->json(['message' => 'Application updated successfully']);
        }
    }


    public  function deleteAdmissionAppication($Application_no)
    {
        $deleted = AdmissionApplication::where('Application_no', $Application_no)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Application was successfully deleted']);
        } else {
            return response()->json(['message' => 'Application deletion failed or no matching application found']);
        }
    }
    public function sendAlertNotification($applicantName, $applicantID, $missingDocuments)
    {
        $email = User::where('id', $applicantID)->pluck('email');
        $recipientEmail = $email[0];
        $notification = new MissingDocumentsNotification($applicantName, $applicantID, $missingDocuments);
        \Notification::route('mail', $recipientEmail)->notify($notification);
    }

    function getAdmissionDetails($filters)
    {

        $perPage = 10;

        $query = AdmissionApplication::query();

        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];

            $query->where(function ($query) use ($searchTerm) {
                $query->where('Application_no', 'LIKE', "%$searchTerm%")
                    ->orWhere('id', 'LIKE', "%$searchTerm%")
                    ->orWhere('user_id', 'LIKE', "%$searchTerm%")
                    ->orWhere('applied_university', 'LIKE', "%$searchTerm%")
                    ->orWhere('status', 'LIKE', "%$searchTerm%")
                    ->orWhere('application_date', 'LIKE', "%$searchTerm%")
                    ->orWhere('course_name', 'LIKE', "%$searchTerm%")
                    ->orWhereHas('user', function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', "%$searchTerm%");
                    });
            });
        }


        $data = $query->paginate($perPage);

        foreach ($data as $application) {
            $userDetails = User::where('id', $application->user_id)->first();

            if ($userDetails) {
                $application->userName = $userDetails->name;
                $application->userEmail = $userDetails->email;
            } else {
                $application->userName = "Unknown User";
                $application->userEmail = "N/A";
            }
            // dd('here');
            $application->required_doc = json_decode($application->required_doc, true);



            if (!empty($application->missing_documents)) {
                $decodedMissingDocs = json_decode($application->missing_documents, true);

                if (is_array($decodedMissingDocs)) {
                    $application->missing_documents = array_values($decodedMissingDocs);
                } else {
                    // Handle case where the JSON string contains a single string value
                    $cleanedValue = trim($application->missing_documents[0], '{}"');
                    $cleanedValue = str_replace('\\"', '"', $cleanedValue);
                    $application->missing_documents = explode(',', $cleanedValue);
                }
            } else {
                $application->missing_documents = [];
            }
        }

        return $data;
    }
    function getStudentAdmissionDetails($id)
    {
        $perPage = 10;
        $data = AdmissionApplication::where('user_id', $id)->paginate($perPage);



        foreach ($data->items() as $item) {



            if (!empty($item->missing_documents)) {
                $decodedMissingDocs = json_decode($item->missing_documents, true);

                if (is_array($decodedMissingDocs)) {
                    $item->missing_documents = array_values($decodedMissingDocs);
                } else {
                    $cleanedValue = trim($item->missing_documents[0], '{}"');
                    $cleanedValue = str_replace('\\"', '"', $cleanedValue);
                    $item->missing_documents = explode(',', $cleanedValue);
                }
            } else {
                $item->missing_documents = [];
            }

            $item->required_doc = json_decode($item->required_doc, true);
        }

        return $data;
    }

    function getProcessAdmissionDoc($id)
    {
        $application = AdmissionApplication::find($id);

        if (!$application) {
            return response()->json(['error' => 'Application not found.'], 404);
        }

        $userDocs = AdminUploadedDocument::where('application_no', $application->application_no)->get();

        $documentList = $userDocs->map(function ($doc) {
            return [
                'doc_name' => $doc->doc_name,
                'url' => $doc->url,
            ];
        });

        if ($application->status === 'Admission Offered' && !is_null($application->offer_letter)) {
            $documentList->push([
                'offer_letter' => $application->offer_letter,
                'offer_letter_url' => $application->url

            ]);
        }

        if ($documentList->isEmpty()) {
            return response()->json(['error' => 'Admission documents not available.'], 204);
        }

        return response()->json(['documents' => $documentList]);
    }



    public function processAdmissionRelatedDoc($data, $applicationId)
    {
        try {
            $application = AdmissionApplication::findOrFail($applicationId);
            if (isset($data['Document']) && $data['Document'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $data['Document'];
                $fileorgName = $file->getClientOriginalName();
                $fileType = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $fileType;
                Storage::disk('public')->put($fileName, file_get_contents($file->getRealPath()));
                $data['Documents'] = $fileName;
                $data['doc_orignial_name'] = $fileorgName;
                $data['doc_type'] = $fileType;
            }



            AdminUploadedDocument::create([
                'user_id' => $application->user_id,
                'application_no' => $application->application_no,
                'doc_type' => $data['doc_type'],
                'doc_name' => $data['Document_name'],
                'doc_path' => $data['Documents'],
            ]);
            if ($application->status === '1' && $data['Document_name'] ==='offer letter') {

                if (isset($data['Documents'])) {
                    $application->update([
                        'status' => 'Admission Offered',
                        'offer_letter' => $data['Documents'],
                    ]);
                } else {
                    $application->update([
                        'status' => 'Admission Offered',
                    ]);
                }
            }

            return response()->json(['message' => 'Application processed successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Application with the specified ID not found'], 404);
        }
    }
    public function processAdmission($data, $applicationId)
    {
        try {
            $application = AdmissionApplication::findOrFail($applicationId);
            if (isset($data['offer_letter']) && $data['offer_letter'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $data['offer_letter'];
                $fileorgName = $file->getClientOriginalName();
                $fileType = $file->getClientOriginalExtension();
                $fileName = time() . '.' . $fileType;
                Storage::disk('public')->put($fileName, file_get_contents($file->getRealPath()));

                // Update the data array with the new file information
                $data['offer_letter'] = $fileName;
                $data['doc_orignial_name'] = $fileorgName;
                $data['doc_type'] = $fileType;
            }

            if ($application->status === '1') {
                if (isset($data['offer_letter'])) {
                    $application->update([
                        'status' => 'Admission Offered',
                        'offer_letter' => $data['offer_letter'],
                    ]);
                } else {
                    $application->update([
                        'status' => 'Admission Offered',
                    ]);
                }
            }

            return response()->json(['message' => 'Application processed successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Application with the specified ID not found'], 404);
        }
    }



    public function displayOfferLetter($applicationId)
    {
        $application = AdmissionApplication::findOrFail($applicationId);

        if ($application->status === 'Admission Offered') {
            return response()->json(['offer_letter' => $application->offer_letter, 'offer_letter' => $application->url]);
        } else {
            return response()->json(['message' => 'Admission offer not available.'], 404);
        }
    }
}
