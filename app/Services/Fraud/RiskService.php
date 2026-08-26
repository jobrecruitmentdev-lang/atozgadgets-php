<?php

namespace App\Services\Fraud;

use App\Models\RiskAssessment;
use App\Models\Order;

class RiskService
{
    public static function evaluate(Order $order, float $capturedAmount, string $capturedCurrency, ?string $clientIp = null): RiskAssessment
    {
        $riskScore = 0;
        $signals = [];
        $decision = 'APPROVE';

        // 1. Critical Amount Verification
        if (abs($capturedAmount - (float)$order->total_amount) > 0.01) {
            $riskScore += 90;
            $signals[] = "Amount mismatch: expected {$order->total_amount}, received {$capturedAmount}";
        }

        // 2. Currency Check
        $expectedCurrency = strtoupper(\App\Models\Setting::get('currency', 'USD'));
        if (strtoupper($capturedCurrency) !== $expectedCurrency) {
            $riskScore += 40;
            $signals[] = "Currency mismatch: expected {$expectedCurrency}, received {$capturedCurrency}";
        }

        // 3. High Value Review Trigger ($1,000+)
        if ((float)$order->total_amount >= 1000.00) {
            $riskScore += 25;
            $signals[] = 'High value order threshold exceeded ($1000+)';
        }

        if ($riskScore >= 70) {
            $riskLevel = 'HIGH';
            $decision = 'REJECT';
        } elseif ($riskScore >= 30) {
            $riskLevel = 'MEDIUM';
            $decision = 'REVIEW';
        } else {
            $riskLevel = 'LOW';
            $decision = 'APPROVE';
        }

        return RiskAssessment::create([
            'order_id' => $order->id,
            'risk_score' => $riskScore,
            'risk_level' => $riskLevel,
            'signals' => $signals,
            'decision' => $decision,
        ]);
    }
}
