<?php

namespace Botble\Extensions\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Extensions\Http\Requests\ExtensionsRequest;
use Botble\Extensions\Models\Extensions;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Extensions\Tables\ExtensionsTable;
use Botble\Extensions\Forms\ExtensionsForm;

class ExtensionsController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/extensions::extensions.name')), route('extensions.index'));
    }

    public function index(ExtensionsTable $table)
    {
        PageTitle::setTitle(trans('plugins/extensions::extensions.name'));

        return $table->renderTable();
    }

    public function create()
    {
        PageTitle::setTitle(trans('plugins/extensions::extensions.create'));

        return ExtensionsForm::create()->renderForm();
    }

    public function store(ExtensionsRequest $request)
    {
        $form = ExtensionsForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('extensions.index'))
            ->setNextUrl(route('extensions.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Extensions $extensions)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $extensions->name]));

        return ExtensionsForm::createFromModel($extensions)->renderForm();
    }

    public function update(Extensions $extensions, ExtensionsRequest $request)
    {
        ExtensionsForm::createFromModel($extensions)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('extensions.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Extensions $extensions)
    {
        return DeleteResourceAction::make($extensions);
    }
}
