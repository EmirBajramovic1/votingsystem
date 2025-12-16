<?php
/**
 * @OA\Info(
 *   title="SecureVote API",
 *   description="Online Voting System API",
 *   version="1.0.0",
 *   @OA\Contact(
 *     email="support@securevote.com",
 *     name="SecureVote Support"
 *   )
 * )
 *
 * @OA\Server(
 *     url="http://localhost/projects/votingsystem/backend/rest",
 *     description="Local API server"
 * )
 *
 * @OA\Server(
 *     url="https://your-production-domain.com/backend/rest",
 *     description="Production API server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="BearerAuth",
 *     type="http",
 *     bearerFormat="JWT",
 *     scheme="bearer"
 * )
 */
?>