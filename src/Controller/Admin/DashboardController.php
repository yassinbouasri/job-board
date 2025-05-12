<?php

namespace App\Controller\Admin;

use App\Entity\Application;
use App\Entity\Job;
use App\Entity\Profile;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private ChartBuilderInterface $chartBuilder,
        private EntityManagerInterface $entityManager,
    ) { }

    public function index(): Response
    {

        return $this->render('admin/index.html.twig', [
            'chart' => $this->getChart(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Job Board');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa-solid fa-chart-simple');
        yield MenuItem::section('Jobs Management');
        yield MenuItem::linkToRoute('Job Board', 'fa fa-home', 'app_job_index');
        yield MenuItem::linkToCrud('Jobs', 'fa fa-briefcase', Job::class);
        yield MenuItem::linkToCrud('Jobs Applications', 'fa fa-check', Application::class);

        yield MenuItem::section('Users Management');
        yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Profiles', 'fa-solid fa-id-card', Profile::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }

    public function configureActions(): Actions
    {
        return parent::configureActions();
    }
    /**
     * @return Chart
     */
    public function getChart(): Chart
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData(
            [
                'labels'   => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                'datasets' => [
                    [
                        'label'           => 'Jobs Posts',
                        'backgroundColor' => 'rgb(255, 99, 132)',
                        'borderColor'     => 'rgb(255, 99, 132)',
                        'data'            => $this->getJobsGroupedByMonth($this->entityManager),
                    ],
                ],
            ]
        );

        $chart->setOptions(
            [
                'scales' => [
                    'y' => [
                        'suggestedMin' => 0,
                        'suggestedMax' => 100,
                    ],
                ],
            ]
        );

        return $chart;
    }

    private function getJobsGroupedByMonth(EntityManagerInterface $entityManager)
    {
        $currentYear = (new \DateTime())->format('Y');
        $jobRepository = $entityManager->getRepository(Job::class);

        $result = $jobRepository->getJobCountForEachMonth($entityManager, $currentYear);

        $data = $result->fetchAllAssociative();

        $jobCounts = array_fill(0, 12, 0);

        foreach ($data as $row) {
            $month = (int) $row['month'] - 1;
            $jobCounts[$month] = (int) $row['jobcount'];

        }

        return $jobCounts;
    }

}
