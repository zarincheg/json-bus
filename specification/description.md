## Data processing framework protocol description
### General
Protocol based on JSON format. Current version includes 4 objects which used in communication between framework services(nodes, workers, clients): Request, Result, Notification and Task. Message exchange occurs via the bus based on the RabbitMQ.
### Request

This section describes request message which sends between clients and nodes(and between nodes, when node represents a client in a pipeline). JSON structure placed in [specification/json/request.json](specification/json/request.json)

The request object fields:
- id

Request ID. Uses for appropriate responses and chaching results.

- clientId

This ID uses for create exclusive notification(queue with node responses) queue for each client. Also can be used for cases in which needed client identification.

- subject

The general purpose of request for determine what client want

- params

Parameters of request. Depends of subject context.

- data

Arbitrary data, which can be provided for node/workers
