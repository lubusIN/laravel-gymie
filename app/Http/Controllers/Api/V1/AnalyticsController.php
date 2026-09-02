<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\InvoiceTransactionResource;
use App\Models\InvoiceTransaction;
use App\Services\Analytics\AnalyticsService;
use App\Support\Analytics\AnalyticsDateRange;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Read-only analytics endpoints for dashboards / reports.
 */
class AnalyticsController extends ApiController
{
    /**
     * Get financial KPIs for a range.
     */
    public function financial(Request $request): JsonResponse
    {
        $this->requirePermission($request, 'ViewAny:Analytics');

        $range = AnalyticsDateRange::fromFilters($request->all());
        $metrics = app(AnalyticsService::class)->financialMetrics($range);

        return response()->json([
            'data' => [
                'range' => [
                    'start' => $range->start->toDateString(),
                    'end' => $range->end->toDateString(),
                ],
                'metrics' => $metrics,
            ],
        ]);
    }

    /**
     * Get membership KPIs for a range.
     */
    public function membership(Request $request): JsonResponse
    {
        $this->requirePermission($request, 'ViewAny:Analytics');

        $range = AnalyticsDateRange::fromFilters($request->all());
        $metrics = app(AnalyticsService::class)->membershipMetrics($range);

        return response()->json([
            'data' => [
                'range' => [
                    'start' => $range->start->toDateString(),
                    'end' => $range->end->toDateString(),
                ],
                'metrics' => $metrics,
            ],
        ]);
    }

    /**
     * Cashflow trend (collected vs expenses).
     */
    public function cashflowTrend(Request $request): JsonResponse
    {
        $this->requirePermission($request, 'ViewAny:Analytics');

        $range = AnalyticsDateRange::fromFilters($request->all());
        $service = app(AnalyticsService::class);

        $days = $range->end->diffInDays($range->start) + 1;
        $grouping = $days <= 31 ? 'day' : 'month';

        $collected = $grouping === 'day'
            ? $service->collectedTrendByDate($range)
            : $service->collectedTrendByMonth($range);

        $expenses = $grouping === 'day'
            ? $service->expenseTrendByDate($range)
            : $service->expenseTrendByMonth($range);

        $labels = collect(array_keys($collected))
            ->merge(array_keys($expenses))
            ->unique()
            ->sort()
            ->values()
            ->all();

        $collectedSeries = [];
        $expenseSeries = [];

        foreach ($labels as $label) {
            $collectedSeries[] = (float) ($collected[$label] ?? 0);
            $expenseSeries[] = (float) ($expenses[$label] ?? 0);
        }

        return response()->json([
            'data' => [
                'grouping' => $grouping,
                'labels' => $labels,
                'series' => [
                    'collected' => $collectedSeries,
                    'expenses' => $expenseSeries,
                ],
            ],
        ]);
    }

    /**
     * Expense breakdown by category for a range.
     */
    public function expenseCategories(Request $request): JsonResponse
    {
        $this->requirePermission($request, 'ViewAny:Analytics');

        $range = AnalyticsDateRange::fromFilters($request->all());
        $rows = app(AnalyticsService::class)->expenseBreakdownByCategory($range, 10);

        return response()->json([
            'data' => $rows->values()->all(),
        ]);
    }

    /**
     * Top plans by collected amount.
     */
    public function topPlans(Request $request): JsonResponse
    {
        $this->requirePermission($request, 'ViewAny:Analytics');

        $range = AnalyticsDateRange::fromFilters($request->all());
        $rows = app(AnalyticsService::class)->topPlansByCollected($range, 5);

        return response()->json([
            'data' => $rows->values()->all(),
        ]);
    }

    /**
     * Recent invoice transactions (payments/refunds).
     */
    public function recentTransactions(Request $request): AnonymousResourceCollection
    {
        $this->requirePermission($request, 'ViewAny:Analytics');

        $limit = (int) $request->query('limit', 5);
        $limit = $limit > 0 ? min($limit, 50) : 5;

        $rows = InvoiceTransaction::query()
            ->with([
                'invoice.subscription.member',
                'invoice.subscription.plan',
            ])
            ->orderByDesc('occurred_at')
            ->limit($limit)
            ->get();

        return InvoiceTransactionResource::collection($rows);
    }
}
