<?

error_reporting(E_ALL);
ini_set('display_error', 1);


require __DIR__.'/vendor/autoload.php';

putenv('GOOGLE_APPLICATION_CREDENTIALS=./painel_de_chamada-65a71bc7554d.json');

// Imports the Cloud Client Library
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;

// instantiates a client
$client = new TextToSpeechClient();

// sets text to be synthesised
$synthesisInputText = (new SynthesisInput())
    ->setText($_POST['text']);

// build the voice request, select the language code ("en-US") and the ssml
// voice gender
$voice = (new VoiceSelectionParams())
    ->setLanguageCode('pt-BR')
    ->setSsmlGender(SsmlVoiceGender::MALE);

// Effects profile
$effectsProfileId = "telephony-class-application";

// select the type of audio file you want returned
$audioConfig = (new AudioConfig())
    ->setAudioEncoding(AudioEncoding::MP3)
    ->setEffectsProfileId(array($effectsProfileId));

// perform text-to-speech request on the text input with selected voice
// parameters and audio file type
$response = $client->synthesizeSpeech($synthesisInputText, $voice, $audioConfig);
$audioContent = $response->getAudioContent();

// the response's audioContent is binary
if(file_exists('chamada.mp3')){
    @unlink('chamada.mp3');
}

file_put_contents('chamada.mp3', $audioContent);
echo 'ok';