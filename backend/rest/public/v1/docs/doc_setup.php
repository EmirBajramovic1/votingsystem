<?php
/**
 * @OA\Info(
 *      title="SecureVote API",
 *      version="1.0.0",
 *      description="Online Voting System API Documentation",
 *      @OA\Contact(
 *          email="support@securevote.com",
 *          name="SecureVote Support"
 *      )
 * ),
 *
 * @OA\Server(
 *      url=LOCALSERVER,
 *      description="Local Development Server"
 * ),
 * @OA\Server(
 *      url=PRODSERVER,
 *      description="Production Server"
 * ),
 *
 * @OA\SecurityScheme(
 *     securityScheme="BearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Add 'Bearer {your_token}' to authorize requests"
 * )
 */
?>