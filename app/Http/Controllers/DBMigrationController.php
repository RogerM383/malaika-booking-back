<?php

namespace App\Http\Controllers;

use App\Services\DatabaseMigrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DBMigrationController extends Controller
{
    private DatabaseMigrationService $dbms;

    public function __construct(DatabaseMigrationService $dbms)
    {
        $this->dbms = $dbms;
    }

    ///**
    // * @OA\Get(
    // *      path="/api/db",
    // *      tags={"Database"},
    // *      summary="Migra la base de datos",
    // *      security={{"bearer_token":{}}},
    // *      description="Migra la base de datos",
    // *      operationId="dbMigrate",
    // *      @OA\Response(
    // *          response="200",
    // *          description="DB migrated successfully",
    // *           @OA\JsonContent(
    // *              type="object",
    // *              @OA\Property(property="data", type="array", @OA\Items())
    // *          )
    // *      )
    // *  )
    // *
    // * @param Request $request
    // * @return JsonResponse
    // */
    //public function migrate(Request $request): JsonResponse
    //{
    //    $this->dbms->migrate();
    //    return $this->sendResponse([],'Data migrated successfully');
    //}
//
    ///**
    // * @OA\Get(
    // *      path="/api/db/2",
    // *      tags={"Database"},
    // *      summary="Migra la base de datos",
    // *      security={{"bearer_token":{}}},
    // *      description="Migra la base de datos",
    // *      operationId="dbMigrate2",
    // *      @OA\Response(
    // *          response="200",
    // *          description="DB migrated successfully",
    // *           @OA\JsonContent(
    // *              type="object",
    // *              @OA\Property(property="data", type="array", @OA\Items())
    // *          )
    // *      )
    // *  )
    // *
    // * @param Request $request
    // * @return JsonResponse
    // */
    //public function migrate2(Request $request): JsonResponse
    //{
    //    $this->dbms->migrate2();
    //    return $this->sendResponse([],'Data migrated successfully');
    //}
//
    ///**
    // * @OA\Get(
    // *      path="/api/db/3",
    // *      tags={"Database"},
    // *      summary="Migra la base de datos",
    // *      security={{"bearer_token":{}}},
    // *      description="Migra la base de datos",
    // *      operationId="dbMigrate3",
    // *      @OA\Response(
    // *          response="200",
    // *          description="DB migrated successfully",
    // *           @OA\JsonContent(
    // *              type="object",
    // *              @OA\Property(property="data", type="array", @OA\Items())
    // *          )
    // *      )
    // *  )
    // *
    // * @param Request $request
    // * @return JsonResponse
    // */
    //public function migrate3(Request $request): JsonResponse
    //{
    //    $this->dbms->migrate3();
    //    return $this->sendResponse([],'Data migrated successfully');
    //}
//
    ///**
    // * @OA\Get(
    // *      path="/api/db/4",
    // *      tags={"Database"},
    // *      summary="Migra la base de datos",
    // *      security={{"bearer_token":{}}},
    // *      description="Migra la base de datos",
    // *      operationId="dbMigrate4",
    // *      @OA\Response(
    // *          response="200",
    // *          description="DB migrated successfully",
    // *           @OA\JsonContent(
    // *              type="object",
    // *              @OA\Property(property="data", type="array", @OA\Items())
    // *          )
    // *      )
    // *  )
    // *
    // * @param Request $request
    // * @return JsonResponse
    // */
    //public function migrate4(Request $request): JsonResponse
    //{
    //    $this->dbms->migrate4();
    //    return $this->sendResponse([],'Data migrated successfully');
    //}
//
    ///**
    // * @OA\Get(
    // *      path="/api/db/5",
    // *      tags={"Database"},
    // *      summary="Migra la base de datos",
    // *      security={{"bearer_token":{}}},
    // *      description="Migra la base de datos",
    // *      operationId="dbMigrate5",
    // *      @OA\Response(
    // *          response="200",
    // *          description="DB migrated successfully",
    // *           @OA\JsonContent(
    // *              type="object",
    // *              @OA\Property(property="data", type="array", @OA\Items())
    // *          )
    // *      )
    // *  )
    // *
    // * @param Request $request
    // * @return JsonResponse
    // */
    //public function migrate5(Request $request): JsonResponse
    //{
    //    $this->dbms->migrate5();
    //    return $this->sendResponse([],'Data migrated successfully');
    //}
//
    ///**
    // * @OA\Get(
    // *      path="/api/db/6",
    // *      tags={"Database"},
    // *      summary="Migra la base de datos",
    // *      security={{"bearer_token":{}}},
    // *      description="Migra la base de datos",
    // *      operationId="dbMigrate6",
    // *      @OA\Response(
    // *          response="200",
    // *          description="DB migrated successfully",
    // *           @OA\JsonContent(
    // *              type="object",
    // *              @OA\Property(property="data", type="array", @OA\Items())
    // *          )
    // *      )
    // *  )
    // *
    // * @param Request $request
    // * @return JsonResponse
    // */
    //public function migrate6(Request $request): JsonResponse
    //{
    //    $this->dbms->migrate6();
    //    return $this->sendResponse([],'Data migrated successfully');
    //}

    /**
     * @OA\Get(
     *      path="/api/db/calculate",
     *      tags={"Database"},
     *      summary="Calculate rooms",
     *      security={{"bearer_token":{}}},
     *      description="Calculate rooms",
     *      operationId="roomCalc",
     *      @OA\Response(
     *          response="200",
     *          description="DB migrated successfully",
     *           @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", type="array", @OA\Items())
     *          )
     *      )
     *  )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function calc(Request $request): JsonResponse
    {
        $this->dbms->updateRoomsNumbers();
        return $this->sendResponse([],'Data updated successfully');
    }

    ///**
    // * @OA\Get(
    // *      path="/api/db/import-type_room",
    // *      tags={"Database"},
    // *      summary="Import travelers.type_room column to clients.room_observations",
    // *      security={{"bearer_token":{}}},
    // *      description="Import travelers.type_room column to clients.room_observations",
    // *      operationId="imnportTypeRooms",
    // *      @OA\Response(
    // *          response="200",
    // *          description="DB migrated successfully",
    // *           @OA\JsonContent(
    // *              type="object",
    // *              @OA\Property(property="data", type="array", @OA\Items())
    // *          )
    // *      )
    // *  )
    // *
    // * @param Request $request
    // * @return JsonResponse
    // */
    //public function importTypeRooms(Request $request): JsonResponse
    //{
    //    $this->dbms->importCommentsRoomType();
    //    return $this->sendResponse([],'Data imported successfully');
    //}
//
    ///**
    // * @OA\Get(
    // *      path="/api/db/trimDNIs",
    // *      tags={"Database"},
    // *      summary="Clear DNI from DB",
    // *      security={{"bearer_token":{}}},
    // *      description="Clear DNI from DB",
    // *      operationId="trimDNIs",
    // *      @OA\Response(
    // *          response="200",
    // *          description="DB DNIs trimmed successfully",
    // *           @OA\JsonContent(
    // *              type="object",
    // *              @OA\Property(property="data", type="array", @OA\Items())
    // *          )
    // *      )
    // *  )
    // *
    // * @param Request $request
    // * @return JsonResponse
    // */
    //public function trimDNIs(Request $request): JsonResponse
    //{
    //    return $this->sendResponse(['TOTAL' => $this->dbms->trimDNIs()],'Data trimed successfully');
    //}
//
    ///**
    // * @OA\Get(
    // *      path="/api/db/import-null-passports",
    // *      tags={"Database"},
    // *      summary="Import null number pasports from DB",
    // *      security={{"bearer_token":{}}},
    // *      description="Import null number pasports from DB",
    // *      operationId="importNullPassports",
    // *      @OA\Response(
    // *          response="200",
    // *          description="Null passports imported successfully",
    // *           @OA\JsonContent(
    // *              type="object",
    // *              @OA\Property(property="data", type="array", @OA\Items())
    // *          )
    // *      )
    // *  )
    // *
    // * @param Request $request
    // * @return JsonResponse
    // */
    //public function importNullPassports(Request $request): JsonResponse
    //{
    //    $this->dbms->migrateNullPassports();
    //    return $this->sendResponse([],'Passport migrated successfully');
    //}
//
//
    ///**
    // * @OA\Get(
    // *      path="/api/db/clear-duplicated-rooms",
    // *      tags={"Database"},
    // *      summary="Import null number pasports from DB",
    // *      security={{"bearer_token":{}}},
    // *      description="Import null number pasports from DB",
    // *      operationId="clearDuplicatedRooms",
    // *      @OA\Response(
    // *          response="200",
    // *          description="Null passports imported successfully",
    // *           @OA\JsonContent(
    // *              type="object",
    // *              @OA\Property(property="data", type="array", @OA\Items())
    // *          )
    // *      )
    // *  )
    // *
    // * @param Request $request
    // * @return JsonResponse
    // */
    //public function clearDuplicatedRooms(Request $request): JsonResponse
    //{
    //    $this->dbms->clearDuplicatedRooms();
    //    return $this->sendResponse([],'Passport migrated successfully');
    //}
}
