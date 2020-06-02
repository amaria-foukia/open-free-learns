<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

class Pagination
{
    private $entityClass;
    private $limit = 12;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, string $templatePath)
    {
        // On récupère le nom de la route à utiliser à partir des attributs de la requête actuelle
        $this->route        = $request->getCurrentRequest()->attributes->get('_route');
        // Autres initialisations
        $this->manager      = $manager;
        $this->twig         = $twig;
        $this->templatePath = $templatePath;
    }

    public function display()
    {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    public function getPages()
    {
        if (empty($this->entityClass)) {
            // Si il n'y a pas d'entité configurée, on ne peut pas charger le repository, la fonction
            // ne peut donc pas continuer !
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }

        // 1) Connaitre le total des enregistrements de la table
        $total = count($this->manager
            ->getRepository($this->entityClass)
            ->findAll());

        // 2) Faire la division, l'arrondi et le renvoyer
        return ceil($total / $this->limit);
    }

    public function getData()
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }
        // 1) Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // 2) Demander au repository de trouver les éléments à partir d'un offset et 
        // dans la limite d'éléments imposée (voir propriété $limit)
        return $this->manager
            ->getRepository($this->entityClass)
            ->findBy([], [], $this->limit, $offset);
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setLimit($limit)
    {

        $this->limit = $limit;
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setPage($page)
    {
        $this->currentPage = $page;
        return $this;
    }

    public function getPage()
    {
        return $this->currentPage;
    }

    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }
}
