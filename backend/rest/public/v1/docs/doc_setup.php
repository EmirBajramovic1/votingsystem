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
 * ),
 * @OA\Server(
 *     url="http://localhost/securevote/backend/rest",
 *     description="API server"
 * ),
 * @OA\Server(
 *     url="https://your-production-domain.com/backend/rest",
 *     description="API server"
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="ApiKey",
 *     type="apiKey",
 *     in="header",
 *     name="Authentication"
 * )
 */
?>