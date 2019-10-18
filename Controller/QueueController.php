<?php declare(strict_types=1);

namespace Ctrl\SQSInsightBundle\Controller;

use Ctrl\SQSInsightBundle\SQS\SQSClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\VarDumper\VarDumper;

class QueueController extends AbstractController
{
    public function index(SQSClient $client): Response
    {
        $queues = $client->getQueues();

        foreach ($queues as $name => $queue) {
            $queues[$name]['messageCount'] = count($client->getAllMessages($queue['url']));
        }

        return $this->render(
            'SQSInsightBundle:Queue:index.html.twig',
            ['queues' => $queues]
        );
    }

    public function messages(SQSClient $client, string $queueUrl): Response
    {
        $queueUrl = urldecode($queueUrl);
        $messages = [];
        foreach ($client->getAllMessages($queueUrl) as $message) {
            /** @var Envelope $envelope */
            $envelope = unserialize(stripslashes($message['Body']));
            $envelopeMessage = $envelope->getMessage();
            $content = $envelopeMessage instanceof \JsonSerializable
                ? json_encode($envelopeMessage, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT)
                : null;
            if ($content === null && method_exists($envelopeMessage, 'toArray')) {
                $content = json_encode($envelopeMessage->toArray(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
            }
            $messages[] = [
                'id' => $message['MessageId'],
                'timestamp' => $message['Attributes']['SentTimestamp'],
                'datetime' => new \DateTime(
                    '@' . floor($message['Attributes']['SentTimestamp'] / 1000)
                ),
                'attributes' => $message['Attributes'],
                'class' => get_class($envelope->getMessage()),
                'content' => $content,
                'raw' => $message['Body'],
            ];
        }

        usort($messages, function ($a, $b) {
            return $a['timestamp'] <=> $b['timestamp'];
        });

        return $this->render(
            'SQSInsightBundle:Queue:messages.html.twig',
            [
                'queueName' => $client->getNameFromUrl($queueUrl),
                'messages' => $messages,
            ]
        );
    }
}
