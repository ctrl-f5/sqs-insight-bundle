<?php declare(strict_types=1);

namespace Ctrl\SQSInsightBundle\SQS;

class SQSClient
{
    private $client;
    private $stripFromName;
    private $ignoreQueues;

    public function __construct(array $clientConfig, string $stripFromName, array $ignoreQueues = [])
    {
        $this->client = new \Aws\Sqs\SqsClient($clientConfig);
        $this->stripFromName = $stripFromName;
        $this->ignoreQueues = $ignoreQueues;
    }

    public function getQueues(): array
    {
        $queueUrls = $this->client->listQueues()->get('QueueUrls');

        $queues = [];
        foreach ($queueUrls as $queueUrl) {
            if (in_array($queueUrl, $this->ignoreQueues, true)) {
                continue;
            }

            $name = $this->getNameFromUrl($queueUrl);
            if (in_array($name, $this->ignoreQueues, true)) {
                continue;
            }

            $queues[$name] = [
                'name' => $name,
                'url' => $queueUrl
            ];
        }

        return $queues;
    }

    public function getAllMessages(string $queueUrl): array
    {
        $allMessages = [];
        while ($messages = $this->getMessages($queueUrl)) {
            $allMessages[] = $messages;
        }

        if (empty($allMessages)) {
            return [];
        }

        return array_merge(...$allMessages);
    }

    public function getMessages(string $queueUrl): ?array
    {
        $result = $this->client->receiveMessage(
            [
                'AttributeNames' => ['All'],
                'MaxNumberOfMessages' => 10,
                'MessageAttributeNames' => ['All'],
                'QueueUrl' => $queueUrl,
                'WaitTimeSeconds' => 0,
                'VisibilityTimeout' => 2,
            ]
        );

        return $result->get('Messages');
    }

    public function getNameFromUrl(string $url): string
    {
        return str_replace($this->stripFromName, '', $url);
    }
}
