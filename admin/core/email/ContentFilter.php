<?php
class ContentFilter {
    private $restrictedKeywords = [
        'password', 'login', 'urgent', 'click here immediately', 'verify account',
        'suspended', 'limited time', 'act now', 'congratulations you have won',
        'make money fast', 'work from home', 'guaranteed income', 'risk-free',
        'viagra', 'cialis', 'weight loss', 'miracle cure', 'lose weight fast',
        'free gift', 'no cost', 'absolutely free', 'limited offer',
        'casino', 'gambling', 'lottery', 'sweepstakes', 'inheritance',
        'nigerian prince', 'transfer funds', 'confidential', 'beneficiary'
    ];
    
    private $suspiciousPatterns = [
        '/\b\d+% (off|discount|guaranteed)\b/i',
        '/\$\d+,?\d* (guaranteed|promised|risk-free)/i',
        '/urgent.{0,20}action.{0,20}required/i',
        '/verify.{0,20}account.{0,20}immediately/i',
        '/click.{0,10}here.{0,10}now/i',
        '/limited.{0,10}time.{0,10}offer/i'
    ];
    
    public function checkContent($content, $subject) {
        $contentLower = strtolower($content . ' ' . $subject);
        $foundKeywords = [];
        
        // Check keywords
        foreach ($this->restrictedKeywords as $keyword) {
            if (strpos($contentLower, $keyword) !== false) {
                $foundKeywords[] = $keyword;
            }
        }
        
        // Check patterns
        foreach ($this->suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $contentLower)) {
                $foundKeywords[] = 'suspicious pattern detected';
                break;
            }
        }
        
        return [
            'needs_approval' => !empty($foundKeywords),
            'flagged_content' => $foundKeywords
        ];
    }
}
?>