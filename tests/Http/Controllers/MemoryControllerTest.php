<?php

namespace Http\Controllers;

use TestCase;

class MemoryControllerTest extends TestCase
{

    /**
     * On request to the /memories uri,
     * if user_id is absent, then
     * return failed response
     */
    public function testIndex_returns_failed_response_on_user_id(): void
    {
        $response = $this->json('/memories')->content();
        $this->assertStringContainsString($response, 'MISSING_USER_ID_PARAM');
        $this->assertStatus(401);
    }

    /**
     * On request to the /memories uri,
     * if user does not meet the
     * eligibility then, return
     * unauthorized response
     */
    public function testIndex_returns_unauthorized_on_accessing_unavailable_data(): void
    {
        $response = $this->get('/memories')->content();
        $this->assertStringContainsString($response, 'UNAUTHORIZED_ACTION');
        $this->assertStatus(401);
    }
}
