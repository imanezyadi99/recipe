<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use FOS\CKEditorBundle\Form\Type\CKEditorType;


class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud):Crud
    {
        return $crud
        ->setEntityLabelInPlural('Contacts')
        ->setEntityLabelInSingular('Contact')
        ->setPageTitle("index","Tous les contacts")
        ->setPaginatorPageSize(20);

    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                   ->hideOnIndex(),
            TextField::new('fullName'),
            TextField::new('email'),
            TextEditorField::new('message')
                   ->hideOnIndex(),
            
            DateTimeField::new('createdAt')
                   ->hideOnForm()
        ];
    }
    
}
