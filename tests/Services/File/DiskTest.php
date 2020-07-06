<?php

namespace Services\File;

use App\Services\File\Disk;
use App\Structures\StructFile;
use App\User;
use Illuminate\Support\Facades\Storage;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\WithoutMiddleware;
use TestCase;
use Illuminate\Http\UploadedFile;

class DiskTest extends TestCase
{

    use WithoutMiddleware, DatabaseMigrations;

    private $diskMock;
    private $user;

    /**
     * Sets up basic requirements for this test
     */
    public function setUp(): void {
        parent::setUp();
        $this->user = factory(User::class)->create();
        Storage::shouldReceive('put')->andReturn();
    }

    /**
     * Mocks if StructFile class is returned on save
     */
    public function test_save(): void
    {
        $this->actingAs($this->user);
        $file = UploadedFile::fake()->image('memory_attachment.jpg');
        $structClass = app(Disk::class)->save($file);
        $this->assertInstanceOf(StructFile::class, $structClass);
    }
}
