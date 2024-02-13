<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customers;
use App\Models\SalesReportHomePictureImages;
use App\Models\SalesReportIDPictureImages;
use App\Models\SalesReports;
use App\Models\SellingPackets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Str;

class SalesController extends Controller
{
    public function index()
    {
        $user = Auth()->user();
        $salesReports = SalesReports::with(['sellingPackets', 'customer', 'id_images', 'home_images'])->where('appliedBy_id', $user->id)->get();
        $customers = Customers::get();
        $sellingPackets = SellingPackets::get();

        // dd($sellingPackets);

        return Inertia::render('Sales/Index', [
            'salesReports' => $salesReports,
            'sellingPackets' => $sellingPackets,
            'customers' => $customers,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth()->user();
        $customer = new Customers();
        $customer->customer_name = $request->customer_name;
        $customer->telephone_number = $request->telephone_number;
        $customer->address = $request->address;
        $customer->addedBy_id = $user->id;
        $customer->save();

        return redirect()->route('dashboard')->with('success', 'Customer added successfully.');
    }

    public function update(Request $request, $id)
    {
        $customer = Customers::findOrFail($id);
        $customer->customer_name = $request->customer_name;
        $customer->telephone_number = $request->telephone_number;
        $customer->address = $request->address;
        $customer->update();

        return redirect()->route('dashboard')->with('success', 'Customer has been updated successfuly.');
    }

    public function destroy($id)
    {
        $customer = Customers::with('salesReport')->findOrFail($id);
        if ($customer->salesReport !== null) {
            foreach ($customer->salesReport as $item) {
                $salesReport = SalesReports::with(['id_images', 'home_images'])->where('id', $item->id)->first();
                // Deleting each picture on db and public path
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

                $item->delete();
            }
        }
        $customer->delete();
        // dd($salesReportId);

        return redirect()->route('dashboard')->with('success', 'Customer has been deleted successfully.');
    }

    public function storeReport(Request $request)
    {
        $reports = SalesReports::latest()->first();
        $reportId = null;

        if ($reports !== null) {
            $lastReportId = $reports->sales_report_id;
            $lastNumber = (int)str_replace('Report-', '', $lastReportId);
            $nextNumber = $lastNumber + 1;
            $reportId = sprintf("Report-%05d", $nextNumber);
        } else {
            $reportId = 'Report-00001';
        }



        $user = auth()->user();
        $countSalesReport = SalesReports::count();
        $salesReport = new SalesReports();
        $salesReport->customer_id = $request->customerId;
        $salesReport->appliedBy_id = $user->id;
        $newSalesReportId = $reportId;
        $salesReport->sales_report_id = $newSalesReportId;
        $salesReport->selling_packet_id = $request->packetId;
        $salesReport->save();

        // check if ID picture & HomePicture has image uploads
        if ($request->hasFile('id_images') && $request->hasFile('home_images')) {
            $IDPicture = $request->file('id_images');
            $HomePictures = $request->file('home_images');

            foreach ($IDPicture as $image) {
                // generate a unique name for image using timestamp and random string
                $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                // store the image in the public folder with the unique name
                $image->move('id_images', $uniqueName);

                // create a new product image record with the product_id and unique name
                SalesReportIDPictureImages::create([
                    'sales_report_id' => $salesReport->id,
                    'identification_id_image' => 'id_images/' . $uniqueName
                ]);
            }

            foreach ($HomePictures as $image) {
                // generate a unique name for image using timestamp and random string
                $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                // store the image in the public folder with the unique name
                $image->move('home_images', $uniqueName);

                // create a new sales report image record with the home_images field
                SalesReportHomePictureImages::create([
                    'sales_report_id' => $salesReport->id,
                    'home_image' => 'home_images/' . $uniqueName,
                ]);
            }

            return redirect()->route('dashboard')->with('success', 'Sales report has been added successfully.');
        } else {
            return back()->with('error', 'Sales report has not been added. Try again!');
        }
    }

    public function deleteIdImage($id)
    {
        $image = SalesReportIDPictureImages::findOrFail($id);
        $imageName = $image->identification_id_image;
        $filepath = public_path($imageName);
        unlink($filepath);
        $image->delete();

        return redirect()->route('dashboard')->with('success', 'Customer ID image has been deleted successfully.');
    }

    public function deleteHomeImage($id)
    {
        $image = SalesReportHomePictureImages::findOrFail($id);
        $imageName = $image->home_image;
        $filepath = public_path($imageName);
        unlink($filepath);
        $image->delete();

        return redirect()->route('dashboard')->with('success', 'Customer home image has been deleted successfully.');
    }

    public function updateReport(Request $request, $id)
    {
        $salesReport = SalesReports::findOrFail($id);
        $salesReport->customer_id = $request->customerId;
        $salesReport->selling_packet_id = $request->packetId;

        // check if ID picture & HomePicture has image uploads
        if ($request->hasFile('id_images') && $request->hasFile('home_images')) {
            $IDPicture = $request->file('id_images');
            $HomePictures = $request->file('home_images');

            foreach ($IDPicture as $image) {
                // generate a unique name for image using timestamp and random string
                $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                // store the image in the public folder with the unique name
                $image->move('id_images', $uniqueName);

                // create a new product image record with the product_id and unique name
                SalesReportIDPictureImages::create([
                    'sales_report_id' => $salesReport->id,
                    'identification_id_image' => 'id_images/' . $uniqueName
                ]);
            }

            foreach ($HomePictures as $image) {
                // generate a unique name for image using timestamp and random string
                $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                // store the image in the public folder with the unique name
                $image->move('home_images', $uniqueName);

                // create a new sales report image record with the home_images field
                SalesReportHomePictureImages::create([
                    'sales_report_id' => $salesReport->id,
                    'home_image' => 'home_images/' . $uniqueName,
                ]);
            }
        } else {
            return back()->with('error', 'Sales report has not been updated. Try again!');
        }

        $salesReport->update();

        return redirect()->route('dashboard')->with('success', 'Sales report has been updated successfully.');
    }

    public function destroyReport($id)
    {
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

        return redirect()->route('dashboard')->with('success', 'Sales report has been deleted successfully.');
    }
}
