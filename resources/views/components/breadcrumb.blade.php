<div class="row mb-3">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0 font-size-18">{{ $title }}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    @isset($li_1)
                        <li class="breadcrumb-item"><a href="{{ route('root') }}">{{ $li_1 }}</a></li>
                    @endisset
                    @isset($li_2)
                        <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $li_2 }}</a></li>
                    @endisset
                    @isset($title)
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    @endisset
                </ol>
            </div>
        </div>
    </div>
</div>