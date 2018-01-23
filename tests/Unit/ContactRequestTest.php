<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactRequestEmail;
use App\ContactRequest;

class ContactRequestTest extends TestCase
{

    public function test_if_it_saves_data_to_database(){
        $data = factory(ContactRequest::class)->raw();
        
        $this->json('POST', '/contactRequest', $data);

        $last_record = ContactRequest::latest()->first()->toArray();

        $this->assertArraySubset($data, $last_record);

        // Clean up database
        ContactRequest::latest()->first()->delete();
    }

    public function test_if_it_sends_an_email(){
        Mail::fake();

        $this->json('POST', '/contactRequest', [
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'phone' => '867-5309',
            'message' => 'PHPunit test: test_if_it_sends_an_email'
        ]);

        Mail::assertSent(ContactRequestEmail::class);

        // Clean up database
        ContactRequest::latest()->first()->delete();
    }

    public function test_if_it_rejects_invalid_data(){
        Mail::fake();
        $last_record_1 = ContactRequest::latest()->first()->toArray();

        // No Name
        $this->json('POST', '/contactRequest', [
            'name' => '',
            'email' => 'jdoe@example.com',
            'phone' => '867-5309',
            'message' => 'PHPunit test: test_if_it_rejects_invalid_data'
        ])->assertStatus(422);

        // No Email
        $this->json('POST', '/contactRequest', [
            'name' => 'John Doe',
            'email' => '',
            'phone' => '867-5309',
            'message' => 'PHPunit test: test_if_it_rejects_invalid_data'
        ])->assertStatus(422);

        // No Message
        $this->json('POST', '/contactRequest', [
            'name' => 'John Doe',
            'email' => 'jdoe@example.com',
            'phone' => '867-5309',
            'message' => ''
        ])->assertStatus(422);

        // Invalid Email
        $this->json('POST', '/contactRequest', [
            'name' => 'John Doe',
            'email' => 'definitely not an email',
            'phone' => '867-5309',
            'message' => 'PHPunit test: test_if_it_rejects_invalid_data'
        ])->assertStatus(422);

        $last_record_2 = ContactRequest::latest()->first()->toArray();

        // Did not add rows to database
        $this->assertEquals($last_record_1, $last_record_2);

        // Did not send email
        Mail::assertNotSent(ContactRequestEmail::class);
    }

}
