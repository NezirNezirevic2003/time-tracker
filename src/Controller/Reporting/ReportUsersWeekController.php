<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Reporting;

use App\Controller\AbstractController;
use App\Export\Spreadsheet\Writer\BinaryFileResponseWriter;
use App\Export\Spreadsheet\Writer\XlsxWriter;
use App\Model\DailyStatistic;
use App\Reporting\WeeklyUserList;
use App\Reporting\WeeklyUserListForm;
use App\Repository\Query\UserQuery;
use App\Repository\UserRepository;
use App\Timesheet\TimesheetStatisticService;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/reporting/users")
 * @Security("is_granted('view_reporting') and is_granted('view_other_reporting') and is_granted('view_other_timesheet')")
 */
final class ReportUsersWeekController extends AbstractController
{
    /**
     * @Route(path="/week", name="report_weekly_users", methods={"GET","POST"})
     */
    public function report(Request $request, TimesheetStatisticService $statisticService, UserRepository $userRepository): Response
    {
        return $this->render(
            'reporting/report_user_list.html.twig',
            $this->getData($request, $statisticService, $userRepository)
        );
    }

    /**
     * @Route(path="/week_export", name="report_weekly_users_export", methods={"GET","POST"})
     */
    public function export(Request $request, TimesheetStatisticService $statisticService, UserRepository $userRepository): Response
    {
        $data = $this->getData($request, $statisticService, $userRepository);

        $content = $this->container->get('twig')->render('reporting/report_user_list_export.html.twig', $data);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($content);

        $writer = new BinaryFileResponseWriter(new XlsxWriter(), 'kimai-export-users-weekly');

        return $writer->getFileResponse($spreadsheet);
    }

    private function getData(Request $request, TimesheetStatisticService $statisticService, UserRepository $userRepository): array
    {
        $currentUser = $this->getUser();
        $dateTimeFactory = $this->getDateTimeFactory();

        $query = new UserQuery();
        $query->setCurrentUser($currentUser);
        $allUsers = $userRepository->getUsersForQuery($query);

        $values = new WeeklyUserList();
        $values->setDate($dateTimeFactory->getStartOfWeek());

        $form = $this->createForm(WeeklyUserListForm::class, $values, [
            'timezone' => $dateTimeFactory->getTimezone()->getName(),
            'start_date' => $values->getDate(),
        ]);

        $form->submit($request->query->all(), false);

        if ($form->isSubmitted() && !$form->isValid()) {
            $values->setDate($dateTimeFactory->getStartOfWeek());
        }

        if ($values->getDate() === null) {
            $values->setDate($dateTimeFactory->getStartOfWeek());
        }

        $start = $dateTimeFactory->getStartOfWeek($values->getDate());
        $end = $dateTimeFactory->getEndOfWeek($values->getDate());

        $previous = clone $start;
        $previous->modify('-1 week');

        $next = clone $start;
        $next->modify('+1 week');

        $dayStats = [];
        $hasData = true;

        if (!empty($allUsers)) {
            $dayStats = $statisticService->getDailyStatistics($start, $end, $allUsers);
        }

        if (empty($dayStats)) {
            $dayStats = [new DailyStatistic($start, $end, $currentUser)];
            $hasData = false;
        }

        return [
            'period_attribute' => 'days',
            'dataType' => $values->getSumType(),
            'report_title' => 'report_weekly_users',
            'box_id' => 'weekly-user-list-reporting-box',
            'export_route' => 'report_weekly_users_export',
            'form' => $form->createView(),
            'current' => $start,
            'next' => $next,
            'previous' => $previous,
            'decimal' => $values->isDecimal(),
            'subReportDate' => $values->getDate(),
            'subReportRoute' => 'report_user_week',
            'stats' => $dayStats,
            'hasData' => $hasData,
        ];
    }
}
