<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Responses\ApiResponse;
use App\Models\File;

class CompanyController extends ApiController
{
    /**
     * @return ApiResponse
     */
    public function index(): ApiResponse
    {
        $companies = Company::all();
        return $this->success(data: $companies);
    }


    /**
     * @param $companyId
     * @return ApiResponse
     */
    public function show($companyId): ApiResponse
    {
        $company = Company::findOrFail($companyId);

        return $this->success(data: $company);
    }


    /**
     * @param StoreCompanyRequest $request
     * @return ApiResponse
     * @throws \Throwable
     */
    public function store(StoreCompanyRequest $request): ApiResponse
    {
        \DB::beginTransaction();

        try {
            if ($request->hasFile('logo')) {
                $uploadedFile = $request->file('logo');
                $filePath = $uploadedFile->store('logos', 'public'); // storage/app/public/logos

                $file = File::create([
                    'path' => $filePath,
                    'mime_type' => $uploadedFile->getClientMimeType(),
                    'size' => $uploadedFile->getSize(),
                    'name' => $uploadedFile->hashName(),
                    'original_name' => $uploadedFile->getClientOriginalName(),
                    'extension' => $uploadedFile->extension(),
                ]);

                $validatedData = $request->validated();
                $validatedData['logo_id'] = $file->id;
            } else {
                $validatedData = $request->validated();
            }

            $company = Company::create($validatedData);

            \DB::commit();

            return $this->success('Company created successfully', $company);
        } catch (\Exception $e) {
            \DB::rollBack();

            return $this->error($e->getMessage(), ['code' => 500]);
        }
    }

    /**
     * @param UpdateCompanyRequest $request
     * @param Company $company
     * @return ApiResponse
     * @throws \Throwable
     */
    public function update(UpdateCompanyRequest $request, Company $company): ApiResponse
    {
        \DB::beginTransaction();

        try {
            if ($request->hasFile('logo')) {
                $uploadedFile = $request->file('logo');
                $filePath = $uploadedFile->store('logos', 'public'); // storage/app/public/logos

                $file = File::create([
                    'path' => $filePath,
                    'mime_type' => $uploadedFile->getClientMimeType(),
                    'size' => $uploadedFile->getSize(),
                    'name' => $uploadedFile->hashName(),
                    'original_name' => $uploadedFile->getClientOriginalName(),
                    'extension' => $uploadedFile->extension(),
                ]);

                // Delete old logo
                if ($company->logo_id) {
                    $oldFile = File::find($company->logo_id);

                    if ($oldFile) {
                        $company->logo_id = null;
                        $company->save();
                        \Storage::disk('public')->delete($oldFile->path);
                        $oldFile->delete();
                    }
                }


                $validatedData = $request->validated();
                $validatedData['logo_id'] = $file->id;
            } else {
                $validatedData = $request->validated();
            }


            $company->update($validatedData);

            \DB::commit();

            return $this->success('Company updated successfully', $company);
        } catch (\Exception $e) {
            \DB::rollBack();

            return $this->error($e->getMessage(), ['code' => 500]);
        }
    }


    /**
     * @param Company $company
     * @return ApiResponse
     * @throws \Throwable
     */
    public function destroy(Company $company): ApiResponse
    {
        \DB::beginTransaction();

        try {
            // Delete logo
            if ($company->logo_id) {
                $oldFile = File::find($company->logo_id);

                if ($oldFile) {
                    $company->logo_id = null;
                    $company->save();
                    \Storage::disk('public')->delete($oldFile->path);
                    $oldFile->delete();
                }
            }

            // Delete company
            $company->delete();

            // end transaction
            \DB::commit();

            return $this->success('Company deleted successfully', $company);
        } catch (\Exception $e) {
            \DB::rollBack();

            return $this->error('Error deleting company: ' . $e->getMessage(), ['code' => 500]);
        }
    }

}
