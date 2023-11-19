<?php

namespace App\Repositories;

use App\Models\Appointment;

final class AppointmentRepository
{

    function viewAppointment($user_id)
    {
        $appoint = Appointment::where('user_id', $user_id)->paginate(10);
        return $appoint;
    }

    public function cancelAppiont($id)
    {
        $appoint = Appointment::where('id', $id)->delete();

        if ($appoint === 0) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        return response()->json(['message' => ' deleted successfully'], 200);
    }
    public function comfirmAppointment($appointment_id)
    {
        $appointment = Appointment::find($appointment_id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $appointment->status = '1';
        $appointment->save();

        return response()->json(['message' => 'Appointment confirmed successfully'], 200);
    }
}
