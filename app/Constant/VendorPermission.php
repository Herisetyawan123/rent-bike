<?php

namespace App\Constant;

class VendorPermission
{
    // DASHBOARD
    public const DASHBOARD_VIEW = 'dashboard.view';

    // MOTOR (BIKE)
    public const BIKE_VIEW      = 'bike.view';
    public const BIKE_CREATE    = 'bike.create';
    public const BIKE_EDIT      = 'bike.edit';
    public const BIKE_DELETE    = 'bike.delete';

    // TRANSAKSI
    public const TRANSAKSI_VIEW    = 'transaksi.view';
    public const TRANSAKSI_CREATE  = 'transaksi.create';
    public const KONTRAK_DOWNLOAD  = 'kontrak.download';

    // KONTRAK
    public const KONTRAK_VIEW      = 'kontrak.view';
    public const KONTRAK_GENERATE  = 'kontrak.generate';

    // PROFILE (vendor profile)
    public const PROFILE_VIEW      = 'profile.view';
    public const PROFILE_EDIT      = 'profile.edit';
}
