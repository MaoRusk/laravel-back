<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddLandContactRequest;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Contact;
use App\Models\Land;
use App\Models\LandContact;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandContactController extends ApiController implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('permission:lands.contacts.show', only: ['show']),
            new Middleware('permission:lands.contacts.create', only: ['store']),
            new Middleware('permission:lands.contacts.update', only: ['update']),
            new Middleware('permission:lands.contacts.destroy', only: ['destroy']),
            new Middleware('permission:lands.contacts.addContact', only: ['addContact']),
        ];
    }

    /**
     * @param Land $land
     * @return ApiResponse
     */
    public function index(Land $land): ApiResponse
    {
        $contacts = LandContact::where('land_id', $land->id)
            ->with('contact')
            ->get()
            ->pluck('contact');

        return $this->success(data: $contacts);
    }

    /**
     * @param Land $land
     * @param Contact $contact
     * @return ApiResponse
     */
    public function show(Land $land, Contact $contact): ApiResponse
    {
        $contact = LandContact::where('land_id', $land->id)
            ->where('contact_id', $contact->id)
            ->with('contact')
            ->get()
            ->pluck('contact');
        return $this->success(data: $contact);
    }

    /**
     * @param StoreContactRequest $request
     * @param Land $land
     * @return ApiResponse
     */
    public function store(StoreContactRequest $request, Land $land): ApiResponse
    {
        try {
            $validated = $request->validated();

            $contact = Contact::withTrashed()->where('email', $validated['email'])->first();

            if ($contact) {
                if ($contact->trashed()) {
                    $contact->restore();
                }
            } else {
                $contact = Contact::create(array_merge($validated, ['is_land_contact' => true]));
            }

            $exists = LandContact::where('land_id', $land->id)
                ->where('contact_id', $contact->id)
                ->exists();

            if ($exists) {
                return $this->error('This contact is already linked to the land.', ['errors' => 422]);
            }

            LandContact::create([
                'land_id' => $land->id,
                'contact_id' => $contact->id,
            ]);

            return $this->success('Contact added successfully', $contact);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ['errors' => 500]);
        }

    }

    /**
     * @param UpdateContactRequest $request
     * @param Land $land
     * @param Contact $contact
     * @return ApiResponse
     */
    public function update(UpdateContactRequest $request, Land $land, Contact $contact): ApiResponse
    {
        try{
            $landContact = LandContact::where('land_id', $land->id)
                ->where('contact_id', $contact->id)
                ->first();
            if($landContact) {
                if ($contact->trashed()) {
                    $contact->restore();
                }

                $contact->update(array_merge(
                    $request->validated(),
                    ['is_land_contact' => true]
                ));
                return $this->success('Contact updated successfully', $contact);

            } else {
                return $this->error('Land with id '. $land->id . ' does not have contact with id '. $contact->id , ['error' => 404]);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ['error' => 500]);
        }
    }

    /**
     * @param Land $land
     * @param Contact $contact
     * @return ApiResponse
     */
    public function destroy(Land $land, Contact $contact): ApiResponse
    {
        try{
            $landContact = LandContact::where('land_id', $land->id)
                ->where('contact_id', $contact->id)
                ->first();
            if($landContact) {

                $landContact->delete();
                $contact->update([
                   'is_land_contact' => false,
                ]);
                return $this->success('Contact deleted successfully', $contact);

            } else {

                return $this->error('Land with id '. $land->id . ' does not have contact with id '. $contact->id , status: 404);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }

    /**
     * @param AddLandContactRequest $request
     * @param Land $land
     * @param Contact $contact
     * @return ApiResponse
     */
    public function addContact(AddLandContactRequest $request, Land $land, Contact $contact): ApiResponse
    {
        try {
            LandContact::create([
                'land_id' => $land->id,
                'contact_id' => $contact->id,
            ]);

            if (!$contact->is_land_contact) {
                $contact->update(['is_land_contact' => true]);
            }

            return $this->success('The contact has been successfully added to the land.', $contact);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }

    }

}
