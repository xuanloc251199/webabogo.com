<?php

namespace Botble\Place\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Place\Http\Requests\PlaceRequest;
use Botble\Place\Models\Place;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Place\Tables\PlaceTable;
use Botble\Place\Forms\PlaceForm;

class PlaceController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/place::place.name')), route('place.index'));
    }

    public function index(PlaceTable $table)
    {
        PageTitle::setTitle(trans('plugins/place::place.name'));

        return $table->renderTable();
    }

    public function create()
    {
        PageTitle::setTitle(trans('plugins/place::place.create'));

        return PlaceForm::create()->renderForm();
    }

    public function store(PlaceRequest $request)
    {
        $form = PlaceForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('place.index'))
            ->setNextUrl(route('place.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Place $place)
    {
        PageTitle::setTitle(trans('core/base::forms.edit_item', ['name' => $place->name]));

        return PlaceForm::createFromModel($place)->renderForm();
    }

    public function update(Place $place, PlaceRequest $request)
    {
        PlaceForm::createFromModel($place)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('place.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Place $place)
    {
        return DeleteResourceAction::make($place);
    }
}
