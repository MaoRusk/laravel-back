<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddBuildingContactRequest;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Models\Building;
use App\Models\BuildingContact;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Responses\ApiResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BuildingContactController extends ApiController implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('permission:buildings.contacts.show', only: ['show']),
            new Middleware('permission:buildings.contacts.create', only: ['store']),
            new Middleware('permission:buildings.contacts.update', only: ['update']),
            new Middleware('permission:buildings.contacts.destroy', only: ['destroy']),
            new Middleware('permission:buildings.contacts.addContact', only: ['addContact']),
        ];
    }

    /**
     * @param Building $building
     * @return ApiResponse
     */
    public function index(Building $building): ApiResponse
    {
        $contacts = BuildingContact::where('building_id', $building->id)
            ->with('contact')
            ->get()
            ->pluck('contact');

        return $this->success(data: $contacts);
    }

    /**
     * @param Building $building
     * @param Contact $contact
     * @return ApiResponse
     */
    public function show(Building $building, Contact $contact): ApiResponse
    {
        $contact = BuildingContact::where('building_id', $building->id)
            ->where('contact_id', $contact->id)
            ->with('contact')
            ->get()
            ->pluck('contact');
        return $this->success(data: $contact);
    }

    /**
     * @param StoreContactRequest $request
     * @param Building $building
     * @return ApiResponse
     */
    public function store(StoreContactRequest $request, Building $building): ApiResponse
    {
        try {
            $validated = $request->validated();

            $contact = Contact::withTrashed()->where('email', $validated['email'])->first();

            if ($contact) {
                if ($contact->trashed()) {
                    $contact->restore();
                }
            } else {
                $contact = Contact::create(array_merge($validated, ['is_buildings_contact' => true]));
            }


            $exists = BuildingContact::where('building_id', $building->id)
                ->where('contact_id', $contact->id)
                ->exists();

            if ($exists) {
                return $this->error('This contact is already linked to the building.', ['errors' => 422]);
            }

            BuildingContact::create([
                'building_id' => $building->id,
                'contact_id' => $contact->id,
            ]);

            return $this->success('Contact added successfully', $contact);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ['errors' => 500]);
        }
    }

    /**
     * @param UpdateContactRequest $request
     * @param Building $building
     * @param Contact $contact
     * @return ApiResponse
     */
    public function update(UpdateContactRequest $request, Building $building, Contact $contact): ApiResponse
    {
        try {
            $buildingContact = BuildingContact::where('building_id', $building->id)
                ->where('contact_id', $contact->id)
                ->first();

            if ($buildingContact) {
                if ($contact->trashed()) {
                    $contact->restore();
                }

                $contact->update(array_merge($request->validated(), ['is_buildings_contact' => true]));
                return $this->success('Contact updated successfully', $contact);
            } else {
                return $this->error('Building does not have this contact', ['errors' => 404]);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ['errors' => 500]);
        }
    }

    /**
     * @param Building $building
     * @param Contact $contact
     * @return ApiResponse
     */
    public function destroy(Building $building, Contact $contact): ApiResponse
    {
        try{
            $buildingContact = BuildingContact::where('building_id', $building->id)
                ->where('contact_id', $contact->id)
                ->first();
            if($buildingContact) {
                $contact->update(
                    ['is_buildings_contact' => false]
                );
                $buildingContact->delete();
                return $this->success('Contact deleted successfully', $contact);

            } else {

                return $this->error('Building with id '. $building->id . ' does not have contact with id '. $contact->id , 404);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * @param Building $building
     * @param Contact $contact
     * @return ApiResponse
     */
    public function addContact(AddBuildingContactRequest $request, Building $building, Contact $contact): ApiResponse
    {
        try {
            BuildingContact::create([
                'building_id' => $building->id,
                'contact_id' => $contact->id,
            ]);

            if (!$contact->is_buildings_contact) {
                $contact->update(['is_buildings_contact' => true]);
            }

            return $this->success('The contact has been successfully added to the building.', $contact);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), status: 500);
        }
    }
}
