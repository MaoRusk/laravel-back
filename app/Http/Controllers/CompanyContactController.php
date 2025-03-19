<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCompanyContactRequest;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Company;
use App\Models\CompanyContact;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CompanyContactController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:companies.contact.show', only: ['show']),
            new Middleware('permission:companies.contact.create', only: ['store']),
            new Middleware('permission:companies.contact.update', only: ['update']),
            new Middleware('permission:companies.contact.destroy', only: ['destroy']),
            new Middleware('permission:companies.contact.addContact', only: ['addContact']),
        ];
    }

    /**
     * @param Company $company
     * @return ApiResponse
     */
    public function index(Company $company): ApiResponse
    {
        $contacts = CompanyContact::where('company_id', $company->id)
            ->with('contact')
            ->get()
            ->pluck('contact');

        return $this->success(data: $contacts);
    }

    /**
     * @param Company $company
     * @param Contact $contact
     * @return ApiResponse
     */
    public function show(Company $company, Contact $contact): ApiResponse
    {
        $contact = CompanyContact::where('company_id', $company->id)
            ->where('contact_id', $contact->id)
            ->with('contact')
            ->get()
            ->pluck('contact');
        return $this->success(data: $contact);
    }

    /**
     * @param StoreContactRequest $request
     * @param Company $company
     * @return ApiResponse
     */
    public function store(StoreContactRequest $request, Company $company): ApiResponse
    {
        try {
            $validated = $request->validated();

            $contact = Contact::withTrashed()->where('email', $validated['email'])->first();

            if ($contact) {
                if ($contact->trashed()) {
                    $contact->restore();
                }
            } else {
                $contact = Contact::create(array_merge($validated, ['is_company_contact' => true]));
            }

            $exists = CompanyContact::where('company_id', $company->id)
                ->where('contact_id', $contact->id)
                ->exists();

            if ($exists) {
                return $this->error('This contact is already linked to the company.', ['errors' => 422]);
            }

            CompanyContact::create([
                'company_id' => $company->id,
                'contact_id' => $contact->id,
            ]);

            return $this->success('Contact added successfully', $contact);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ['errors' => 500]);
        }

    }

    /**
     * @param UpdateContactRequest $request
     * @param Company $company
     * @param Contact $contact
     * @return ApiResponse
     */
    public function update(UpdateContactRequest $request, Company $company, Contact $contact): ApiResponse
    {
        try{
            $companyContact = CompanyContact::where('company_id', $company->id)
                ->where('contact_id', $contact->id)
                ->first();
            if($companyContact) {
                if ($contact->trashed()) {
                    $contact->restore();
                }

                $contact->update(array_merge(
                    $request->validated(),
                    ['is_company_contact' => true]
                ));
                return $this->success('Contact updated successfully', $contact);

            } else {
                return $this->error('Company with id '. $company->id . ' does not have contact with id '. $contact->id , ['error' => 404]);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ['error' => 500]);
        }
    }

    /**
     * @param Company $company
     * @param Contact $contact
     * @return ApiResponse
     */
    public function destroy(Company $company, Contact $contact): ApiResponse
    {
        try{
            $companyContact = CompanyContact::where('company_id', $company->id)
                ->where('contact_id', $contact->id)
                ->first();
            if($companyContact) {

                $contact->delete();
                return $this->success('Contact deleted successfully', $contact);

            } else {

                return $this->error('Company with id '. $company->id . ' does not have contact with id '. $contact->id , status: 404);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }

    /**
     * @param AddCompanyContactRequest $request
     * @param Company $company
     * @param Contact $contact
     * @return ApiResponse
     */
    public function addContact(AddCompanyContactRequest $request, Company $company, Contact $contact): ApiResponse
    {
        try {
            CompanyContact::create([
                'company_id' => $company->id,
                'contact_id' => $contact->id,
            ]);

            if (!$contact->is_company_contact) {
                $contact->update(['is_company_contact' => true]);
            }

            return $this->success('The contact has been successfully added to the company.', $contact);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }

    }

}
