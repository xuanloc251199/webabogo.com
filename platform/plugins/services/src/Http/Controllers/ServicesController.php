<?php

namespace Botble\Services\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Services\Http\Requests\ServicesRequest;
use Botble\Services\Models\Services;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Services\Tables\ServicesTable;
use Botble\Services\Forms\ServicesForm;

class ServicesController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/services::services.name')), route('services.index'));
    }

    public function index(ServicesTable $table)
    {
        PageTitle::setTitle(trans('plugins/services::services.name'));

        return $table->renderTable();
    }

    public function create()
    {
        PageTitle::setTitle(trans('plugins/services::services.create'));

        return ServicesForm::create()->renderForm();
    }

    public function store(ServicesRequest $request)
    {
        $form = ServicesForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('services.index'))
            ->setNextUrl(route('services.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Services $services)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $services->name]));

        return ServicesForm::createFromModel($services)->renderForm();
    }

    public function update(Services $services, ServicesRequest $request)
    {
        ServicesForm::createFromModel($services)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('services.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Services $services)
    {
        return DeleteResourceAction::make($services);
    }
}
