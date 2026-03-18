<div class="mb-3">
    <label class="form-label" for="policy" class="form-label">{{ __('Policies') }}</label>
    {!! Form::textarea('policy',BaseHelper::cleanEditorContent($policy) , [
                                                'class' => 'form-control',
                                                'placeholder' => 'Policy',
                                                'rows' => 3,
                                            ]) !!}
</div>
<div class="mb-3">
    <label class="form-label" for="rule" class="form-label">{{ __('Rules') }}</label>
    {!! Form::textarea('rule',BaseHelper::cleanEditorContent($rule) , [
                                                'class' => 'form-control',
                                                'placeholder' => 'Rule',
                                                'rows' => 3,
                                            ]) !!}
</div>
