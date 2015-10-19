## Data processing framework protocol description

Messaging based on JSON format. Current version includes 4 objects which used in communication between framework services(nodes, workers, clients): Request, Result, Notification and Task. Message exchange occurs via the bus based on the RabbitMQ.

### Messages description

#### Request

This section describes request message which sends between clients and nodes(and between nodes, when node represents a client in a pipeline).
JSON structure placed in [src/MessagesSchema/request.json](../src/MessagesSchema/request.json)

##### The request object fields:
- **id**<br>
Request ID. Uses for appropriate responses and chaching results.

- **clientId**<br>
This ID uses for create exclusive notification(queue with node responses) queue for each client. Also can be used for cases in which needed client identification.

- **subject**<br>
The general purpose of request for determine what client want

- **params**<br>
Parameters of request. Depends of subject context.

- **data**<br>
Arbitrary data, which can be provided for node/workers

#### Task
Task object is created based on the client request and can be sent to the jobs queue for processing by workers. If data required from another node, the task will have "wait" status before those data will be received.
JSON structure placed in [src/MessagesSchema/task.json](../src/MessagesSchema/task.json)

##### The task object fields:

- **id**<br>
Task ID

- **requestId**<br>
Client request ID for bind request with task

- **createTime**<br>
Time when task was created

- **status**<br>
Task status can be:<br>
*active* - Task placed in the job queue or processing by worker<br>
*wait* - Task requires data from another node and still waiting for<br>
*fail* - Task failed<br>
*complete* - Task was successfully completed

- **params**<br>
Task parameters, which needed for perform the task

- **data**<br>
Arbitrary data, which can be provided for workers

#### Job
Job object is created based on the task and can be sent to the jobs queue for processing by workers. If job will failed by any reason it can be pushed in the queue for new attempt of execution.
JSON structure placed in [src/MessagesSchema/job.json](../src/MessagesSchema/job.json)

##### The job object fields:

- **id**<br>
Job ID

- **taskId**<br>
Task ID for bind job with task

- **createTime**<br>
Time when job was created

- **status**<br>
Job status can be:<br>
*new* - Job is ready for process
*success* - Job has been processed by worker successfully<br>
*fail* - Job failed<br>

- **params**<br>
Job parameters. Not required.

- **data**<br>
Arbitrary data, which can be provided for workers. Not required.

#### Notification
Messages with this type will sent to the clients(through the notifications queue) as a response for their requests.
JSON structure placed in [src/MessagesSchema/notification.json](../src/MessagesSchema/notification.json)

##### The notification object fields:

- **requestId**<br>
Client request ID

- **status**<br>
Notification status can be:<br>
*success* - Request was handled and result is ready<br>
*fail* - Request failed

- **message**<br>
Message with any details

- **data**<br>
Object with additional data

#### Result
Messages with this type describes result of worker's job
JSON structure placed in [src/MessagesSchema/result.json](../src/MessagesSchema/result.json)

##### The result object fields:

- **job**<br>
Job(Job message object) which has been executed. With changed status.

- **data**<br>
Result data
