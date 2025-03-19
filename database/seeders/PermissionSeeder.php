<?php
namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            ['name' => 'users.show'],
            ['name' => 'users.create'],
            ['name' => 'users.update'],
            ['name' => 'users.destroy'],

            ['name' => 'roles.show'],
            ['name' => 'roles.create'],
            ['name' => 'roles.update'],
            ['name' => 'roles.destroy'],

            ['name' => 'buildings.show'],
            ['name' => 'buildings.create'],
            ['name' => 'buildings.update'],
            ['name' => 'buildings.destroy'],
            ['name' => 'buildings.approve'],
            ['name' => 'buildings.draft'],

            ['name' => 'buildings.uploadFiles'],
            ['name' => 'buildings.layoutDesign'],


            ['name' => 'buildings.availability.show'],
            ['name' => 'buildings.availability.create'],
            ['name' => 'buildings.availability.update'],
            ['name' => 'buildings.availability.destroy'],
            ['name' => 'buildings.availability.to-absorption'],


            ['name' => 'buildings.absorption.show'],
            ['name' => 'buildings.absorption.create'],
            ['name' => 'buildings.absorption.update'],
            ['name' => 'buildings.absorption.destroy'],
            ['name' => 'buildings.absorption.to-available'],

            ['name' => 'buildings.contacts.show'],
            ['name' => 'buildings.contacts.create'],
            ['name' => 'buildings.contacts.update'],
            ['name' => 'buildings.contacts.destroy'],
            ['name' => 'buildings.contacts.addContact'],

            ['name' => 'industrial-parks.show'],
            ['name' => 'industrial-parks.create'],
            ['name' => 'industrial-parks.update'],
            ['name' => 'industrial-parks.destroy'],


            ['name' => 'tenants.show'],
            ['name' => 'tenants.create'],
            ['name' => 'tenants.update'],
            ['name' => 'tenants.destroy'],


            ['name' => 'developers.show'],
            ['name' => 'developers.create'],
            ['name' => 'developers.update'],
            ['name' => 'developers.destroy'],


            ['name' => 'brokers.show'],
            ['name' => 'brokers.create'],
            ['name' => 'brokers.update'],
            ['name' => 'brokers.destroy'],

            ['name' => 'industries.show'],
            ['name' => 'industries.create'],
            ['name' => 'industries.update'],
            ['name' => 'industries.destroy'],

            ['name' => 'companies.show'],
            ['name' => 'companies.create'],
            ['name' => 'companies.update'],
            ['name' => 'companies.destroy'],


            ['name' => 'companies.contact.show'],
            ['name' => 'companies.contact.create'],
            ['name' => 'companies.contact.update'],
            ['name' => 'companies.contact.destroy'],
            ['name' => 'companies.contact.addContact'],

            ['name' => 'lands.absorption.show'],
            ['name' => 'lands.absorption.create'],
            ['name' => 'lands.absorption.update'],
            ['name' => 'lands.absorption.destroy'],


            ['name' => 'lands.available.show'],
            ['name' => 'lands.available.create'],
            ['name' => 'lands.available.update'],
            ['name' => 'lands.available.destroy'],

            ['name' => 'lands.contacts.show'],
            ['name' => 'lands.contacts.create'],
            ['name' => 'lands.contacts.update'],
            ['name' => 'lands.contacts.destroy'],
            ['name' => 'lands.contacts.addContact'],

            ['name' => 'lands.show'],
            ['name' => 'lands.create'],
            ['name' => 'lands.update'],
            ['name' => 'lands.destroy'],

            ['name' => 'reit-types.show'],
            ['name' => 'reit-types.create'],
            ['name' => 'reit-types.update'],
            ['name' => 'reit-types.destroy'],

            ['name' => 'reits.show'],
            ['name' => 'reits.create'],
            ['name' => 'reits.update'],
            ['name' => 'reits.destroy'],


            ['name' => 'reit-annual.show'],
            ['name' => 'reit-annual.create'],
            ['name' => 'reit-annual.update'],
            ['name' => 'reit-annual.destroy'],

            ['name' => 'reit-mortgage.show'],
            ['name' => 'reit-mortgage.create'],
            ['name' => 'reit-mortgage.update'],
            ['name' => 'reit-mortgage.destroy'],

            ['name' => 'cams.show'],
            ['name' => 'cams.create'],
            ['name' => 'cams.update'],
            ['name' => 'cams.destroy'],

            ['name' => 'market-growths.show'],
            ['name' => 'market-growths.create'],
            ['name' => 'market-growths.update'],
            ['name' => 'market-growths.destroy'],

            ['name' => 'reits-timeline.show'],
            ['name' => 'reits-timeline.create'],
            ['name' => 'reits-timeline.update'],
            ['name' => 'reits-timeline.destroy'],

            ['name' => 'reit-instruments.show'],
            ['name' => 'reit-instruments.create'],
            ['name' => 'reit-instruments.update'],
            ['name' => 'reit-instruments.destroy'],

            ['name' => 'reit-cetes.show'],
            ['name' => 'reit-cetes.create'],
            ['name' => 'reit-cetes.update'],
            ['name' => 'reit-cetes.destroy'],
        ];
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}

