<div class="appBottomMenu">
    <a href="/dashboard" class="item {{ request()->is ('dashboard') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="home"></ion-icon>
            <strong>Home</strong>
        </div>
    </a>
    <a href="/rekap" class="item {{ request()->is ('rekap') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="calendar" role="img" class="md hydrated"
                aria-label="calendar outline"></ion-icon>
            <strong>Rekap</strong>
        </div>
    </a>
    <a href="/presensi/create" class="item">
        <div class="col">
            <div class="action-button large">
                <ion-icon name="camera" role="img" class="md hydrated" aria-label="add outline"></ion-icon>
            </div>
        </div>
    </a>
    <a href="/izin" class="item {{ request()->is ('izin') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="document-text" role="img" class="md hydrated" aria-label="document text outline"></ion-icon>
            <strong>Perizinan</strong>
        </div>
    </a>
    <a href="/editprofile" class="item {{ request()->is ('editprofile') ? 'active' : '' }}">
        <div class="col">
            <ion-icon name="people" role="img" class="md hydrated" aria-label="people outline"></ion-icon>
            <strong>Profile</strong>
        </div>
    </a>
</div>
