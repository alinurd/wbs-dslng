<?php

namespace App\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;

class InnovativeCaptcha extends Component
{
    public $captchaVerified = false;
    public $currentChallenge = 'pattern';
    public $challengeData = [];
    public $userInput = [];
    public $errorMessage = '';
    public $componentId;
    public $isRefreshing = false;
    
    public $patternLength = 4;
    public $patternGridSize = 9;

    protected $listeners = [
        'refreshCaptcha' => 'generateNewChallenge',
        'verifyPattern' => 'verifyPattern', 
    ];

    public function mount($patternLength = 4, $gridSize = 9)
    {
        $this->componentId = 'captcha-' . Str::random(8);
        $this->patternLength = $patternLength;
        $this->patternGridSize = $gridSize;
        $this->generateNewChallenge();
    }

    public function generateNewChallenge()
    {
        $this->isRefreshing = true;
        $this->errorMessage = '';
        $this->userInput = [];
        $this->captchaVerified = false;
        
        $this->currentChallenge = 'pattern';
        $this->generateChallengeData();
        
        usleep(200000);
        
        $this->isRefreshing = false;
        $this->dispatch('captchaReset');
        $this->dispatch('new-challenge-generated');
    }

    private function generateChallengeData()
    {
        $pattern = [];
        
        for ($i = 0; $i < $this->patternLength; $i++) {
            $pattern[] = rand(0, $this->patternGridSize - 1);
        }
        
        $this->challengeData = [
            'type' => 'pattern',
            'pattern' => $pattern,
            'userPattern' => [],
            'gridSize' => $this->patternGridSize
        ];
    }

    public function verifyPattern($pattern)
    {
        try {
            $this->userInput = $pattern;
            
            if (!isset($this->challengeData['pattern'])) {
                $this->generateNewChallenge();
                return false;
            }
            
            $expectedPattern = $this->challengeData['pattern'];
            $userPattern = $pattern['pattern'] ?? $pattern;
            
            $userArray = is_array($userPattern) ? $userPattern : [$userPattern];
            $expectedArray = $expectedPattern;
            
            $isVerified = $userArray === $expectedArray;
            
            if ($isVerified) {
                $this->captchaVerified = true;
                $this->errorMessage = '';
                $this->dispatch('captchaVerified');
                return true;
            } else {
                $this->errorMessage = __('captcha.pattern_mismatch');
                $this->dispatch('patternMismatch');
                $this->generateNewChallenge();
                return false;
            }
        } catch (\Exception $e) {
            $this->errorMessage = __('captcha.error_occurred');
            $this->generateNewChallenge();
            return false;
        }
    }

    public function render()
    {
        return view('livewire.captcha');
    }
}