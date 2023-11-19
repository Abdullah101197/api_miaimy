<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\AdmissionApplicationService;

class AdmissionApplicationController extends Controller
{

    protected $AdmissionApplicationService;
    public function __construct(AdmissionApplicationService  $AdmissionApplicationService)
    {
        $this->AdmissionApplicationService = $AdmissionApplicationService;
    }

    public function applyForAdmission(Request $request)
    {
        $data = $request->all();
        $result = $this->AdmissionApplicationService->applyForAdmission($data);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    public function updateApplyForAdmission(Request $request)
    {
        $data = $request->all();
        $result = $this->AdmissionApplicationService->updateApplyForAdmission($data);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    
    public function deleteAppication($Application_no)
    {
        $result = $this->AdmissionApplicationService->deleteAdmissionAppication($Application_no);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    public function getStudentAdmissionDetails($id)
    {
        $result = $this->AdmissionApplicationService->getStudentAdmissionDetails($id);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    public function getProcessAdmissionDoc($id)
    {
        $result = $this->AdmissionApplicationService->getProcessAdmissionDoc($id);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    
    public function getAdmissionDetails(Request $request)
    {
        $filters = $request->all();

        $result = $this->AdmissionApplicationService->getAdmissionDetails($filters);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    
    public function processAdmissionRelatedDoc(Request $request, $applicationId)
    {
        $data = $request->all();
        $result = $this->AdmissionApplicationService->processAdmissionRelatedDoc($data, $applicationId);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }
    public function processAdmission(Request $request, $applicationId)
    {
        $data = $request->all();
        $result = $this->AdmissionApplicationService->processAdmission($data, $applicationId);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }

    public function displayOfferLetter($applicationId)
    {
        $result = $this->AdmissionApplicationService->displayOfferLetter($applicationId);
        $statusCode = Response::HTTP_OK;
        return response()->json($result, $statusCode)->setStatusCode(200);
    }

    private function sendAlertNotification($applicantName, $missingDocuments)
    {
        // Implement the logic to send the alert notification (e.g., email, push notification, etc.)
    }
}
