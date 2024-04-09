<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\DestroyRequest;
use App\Http\Requests\Task\IndexRequest;
use App\Http\Requests\Task\ShowRequest;
use App\Http\Requests\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\TaskResource;
use App\Services\Task\CreateTaskDTO;
use App\Services\Task\TaskService;
use App\Services\Task\UpdateTaskDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService) {}

    /**
     * @param IndexRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(IndexRequest $request): AnonymousResourceCollection
    {
       $tasks = $this->taskService->get($request->getPerPage(), $request->getPage());

       return TaskResource::collection($tasks);
    }

    /**
     * @param StoreRequest $request
     * @return TaskResource
     */
    public function store(StoreRequest $request): TaskResource
    {
        $createdTask = new CreateTaskDTO(
            name: $request->getName(),
            description: $request->getDescription(),
            dueDate: $request->getDueDate(),
            status: $request->getStatus(),
            assignedUserId: $request->getAssignedUserId()
        );

        $taskEntity = $this->taskService->create($createdTask);

        return new TaskResource($taskEntity);
    }

    /**
     * @param ShowRequest $request
     * @return TaskResource
     */
    public function show(ShowRequest $request): TaskResource
    {
        $taskEntity = $this->taskService->find($request->getTaskId());

        return new TaskResource($taskEntity);
    }

    /**
     * @param UpdateRequest $request
     * @return TaskResource
     */
    public function update(UpdateRequest $request): TaskResource
    {
        $updatedTaskDTO = new UpdateTaskDTO(
            name: $request->getName(),
            description: $request->getDescription(),
            dueDate: $request->getDueDate(),
            status: $request->getStatus(),
            assignedUserId: $request->getAssignedUserId(),
            isNamePresent: $request->isNamePresent(),
            isDescriptionPresent: $request->isDescriptionPresent(),
            isDueDatePresent: $request->isDueDatePresent(),
            isStatusPresent: $request->isStatusPresent(),
            isAssignedUserIdPresent: $request->isAssignedUserIdPresent(),
        );

        $taskEntity = $this->taskService->update($request->getId(), $updatedTaskDTO);

        return new TaskResource($taskEntity);
    }

    /**
     * @param DestroyRequest $request
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request): JsonResponse
    {
        $this->taskService->delete($request->getId());

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
