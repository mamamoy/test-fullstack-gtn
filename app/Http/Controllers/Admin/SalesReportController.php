<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesReports;
use App\Models\SellingPackets;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class SalesReportController extends Controller
{
    public function index()
    {
        $users = User::where('isAdmin', 0)->with('sales_reports')->get();
        // dd($users);
        $salesReports = SalesReports::with(['user','sellingPackets', 'customer', 'id_images', 'home_images'])->orderBy('isApproved', 'asc')->latest('sales_report_id', 'desc')->get();
        $sellingPackets = SellingPackets::get();
        return Inertia::render('Admin/Index', [
            'users' => $users,
            'salesReports' => $salesReports,
            'sellingPackets' => $sellingPackets
        ]);
    }

    public function store(Request $request)
    {
        $duplicateEmail = User::where('email', $request->email)->first();
        if ($duplicateEmail !== null) {
            return redirect()->route('admin.dashboard')->with('error', 'Email has been used, Use another email');
        } else {
            $sales = new User();
            $sales->name = $request->name;
            $sales->email = $request->email;
            $sales->password = Hash::make($request->password);
            $sales->isAdmin = $request->isAdmin;

            $user = User::where('isAdmin', 0)->latest()->first();
            $nip = $user->NIP;
            $nip = intval(str_replace('Sales-', '', $nip));
            $nip = 'Sales-' . str_pad(($nip + 1), 6, "0", STR_PAD_LEFT);
            $sales->NIP = $nip;
            $sales->save();

            return redirect()->route('admin.dashboard')->with('success', 'Sales added successfully.');
        }
    }

    public function update(Request $request, $id)
    {
        $sales = User::findOrfail($id);

        $duplicateEmail = User::where('email', $request->email)->where('id', '!=', $id)->first();
        // dd($duplicateEmail);

        if ($duplicateEmail) {
            return redirect()->back()->with('error', 'Email has been used, try another email!');
        }

        $sales->name = $request->name;
        $sales->email = $request->email;
        $sales->update();

        return to_route('admin.dashboard')->with('success', 'Sales updated successfully.');
    }

    public function destroy($id)
    {
        $sales = User::with('sales_reports')->findOrFail($id);

        $customers = $sales->sales_reports()->get();
        if ($customers !== null) {
            foreach ($customers as $customer) {
                $customer->delete();
            }
        }
        $sales->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Sales has been deleted successfully.');
    }

    public function approved(Request $request, $id){
        $salesReport = SalesReports::findOrFail($id);
        $user = Auth()->user();

        $salesReport->isApproved = $request->isApproved;
        $salesReport->approvedBy_id = $user->id;
        $salesReport->update();

        return redirect()->route('admin.dashboard')->with('success', 'Sales report has been approved successfully.');
    }

    public function destroyReport($id){
        $salesReport = SalesReports::with(['id_images', 'home_images'])->findOrFail($id);
        if ($salesReport->id_images->count() > 0) {
            foreach ($salesReport->id_images as $IDPicture) {
                $imageName = $IDPicture->identification_id_image;
                $filepath = public_path($imageName);

                if (file_exists($filepath)) {
                    unlink($filepath);
                }
                $IDPicture->delete();
            }
        }

        if ($salesReport->home_images->count() > 0) {
            foreach ($salesReport->home_images as $HomePicture) {
                $imageName = $HomePicture->home_image;
                $filepath = public_path($imageName);

                if (file_exists($filepath)) {
                    unlink($filepath);
                }
                $HomePicture->delete();
            }
        }

        $salesReport->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Sales report has been deleted successfully.');
    }

    public function exportPdf(){
        $userData = User::with(['sales_reports' => function($query) {
            $query->with('sellingPackets');
        }])->where('isAdmin', 0)->get();
        
        $earningUsers = [];
        $subtotal = 0;
        
        foreach($userData as $user){
            $totalEarnings = 0;
        
            foreach($user->sales_reports as $report){
                // dd($report);
                $totalEarnings += $report->sellingPackets->packet_price;
            }
        
            $earningUsers[$user->id] = $totalEarnings;
            $subtotal += $totalEarnings;
        }
        $data = [
            'user' => Auth()->user(),
            'userData' => $userData,
            'earningUsers' => $earningUsers,
            'subtotal' => $subtotal,
        ];
        $pdf = PDF::loadView('pdf', [
            'user' => Auth()->user(),
            'userData' => $userData,
            'earningUsers' => $earningUsers,
            'subtotal' => $subtotal,
        ]);
    
        return $pdf->download('report.pdf');
    }
}
