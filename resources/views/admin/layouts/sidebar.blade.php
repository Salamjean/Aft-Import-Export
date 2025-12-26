<aside class="mdc-drawer mdc-drawer--dismissible mdc-drawer--open" style="background-color: red">
  <div class="mdc-drawer__header">
    <a href="{{route('admin.dashboard')}}" class="brand-logo">
      <img src="{{asset('assets/img/aft.jpg')}}" style="width: 50%; margin-left: 50px" alt="logo">
    </a>
  </div>
  <div class="mdc-drawer__content">
    <div class="user-info">
      <p class="name text-center text-black"> {{Auth::guard('admin')->user()->name}} </p>
      <p class="email text-center text-black">{{Auth::guard('admin')->user()->email}}</p>
    </div>
    <div class="mdc-list-group">
      <nav class="mdc-list mdc-drawer-menu">
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-drawer-link" href="{{route('admin.dashboard')}}">
            <i class="fas fa-home mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
            Tableau de bord
          </a>
        </div>
        {{-- <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-drawer-link" href="#">
            <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">save</i>
            Demandes
          </a>
        </div> --}}
        {{-- <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-drawer-link" href="{{route('admin.permanent-personnel.create')}}">
            <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">dashboard</i>
            Personnel permanent
          </a>
        </div> --}}
        {{-- <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-drawer-link" href="#">
            <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">dashboard</i>
            Historiques des visites
          </a>
        </div> --}}
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-perso">
            <i class="fas fa-home-user mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
            Agence / Agent
            <i class="mdc-drawer-arrow material-icons">chevron_right</i>
          </a>
          <div class="mdc-expansion-panel" id="ui-sub-perso">
            <nav class="mdc-list mdc-drawer-submenu">
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('agence.create')}}">
                  Agence
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('agent.create')}}">
                  Agent
                </a>
              </div>
              {{-- <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="#">
                  Structure desactivées
                </a>
              </div> --}}
            </nav>
          </div>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-agent">
            <i class="fas fa-file-invoice-dollar mdc-list-item__start-detail mdc-drawer-item-icon"
              aria-hidden="true"></i>
            Gestion Devis
            <i class="mdc-drawer-arrow material-icons">chevron_right</i>
          </a>
          <div class="mdc-expansion-panel" id="ui-sub-agent">
            <nav class="mdc-list mdc-drawer-submenu">
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('admin.devis.list.attente')}}">
                  En attente
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('admin.devis.list.confirmed')}}">
                  Confirmé/Validé
                </a>
              </div>
            </nav>
          </div>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-colis">
            <i class="fas fa-box mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
            Gestion Colis
            <i class="mdc-drawer-arrow material-icons">chevron_right</i>
          </a>
          <div class="mdc-expansion-panel" id="ui-sub-colis">
            <nav class="mdc-list mdc-drawer-submenu">
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('colis.create')}}">
                  Ajouter
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('colis.index')}}">
                  Colis enregistré
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('colis.history')}}">
                  Historiques
                </a>
              </div>
            </nav>
          </div>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-conteneur">
            <i class="fas fa-boxes mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
            Gestion Conteneur
            <i class="mdc-drawer-arrow material-icons">chevron_right</i>
          </a>
          <div class="mdc-expansion-panel" id="ui-sub-conteneur">
            <nav class="mdc-list mdc-drawer-submenu">
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('conteneur.create')}}">
                  Ajouter
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('conteneur.index')}}">
                  Listes
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('conteneur.history')}}">
                  Historiques
                </a>
              </div>
            </nav>
          </div>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-menu">
            <i class="fas fa-qrcode mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
            Scanner
            <i class="mdc-drawer-arrow material-icons">chevron_right</i>
          </a>
          <div class="mdc-expansion-panel" id="ui-sub-menu">
            <nav class="mdc-list mdc-drawer-submenu">
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('scan.entrepot')}}">
                  Mise en entrépot
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('scan.charge')}}">
                  Chargement
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('scan.decharge')}}">
                  Déchargment
                </a>
              </div>
            </nav>
          </div>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-code">
            <i class="fas fa-calendar-alt mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
            Planning voyage
            <i class="mdc-drawer-arrow material-icons">chevron_right</i>
          </a>
          <div class="mdc-expansion-panel" id="ui-sub-code">
            <nav class="mdc-list mdc-drawer-submenu">
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('bateau.planifier')}}">
                  Planifier
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('bateau.index')}}">
                  Liste
                </a>
              </div>
              {{-- <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="#">
                  Structure desactivées
                </a>
              </div> --}}
            </nav>
          </div>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-drawer-link" href="{{route('chauffeur.create')}}">
            <i class="fas fa-user-tie mdc-list-item__start-detail mdc-drawer-item-icon"></i>
            Chauffeur
          </a>
        </div>

        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-pro">
            <i class="fas fa-calendar-alt mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
            Programme
            <i class="mdc-drawer-arrow material-icons">chevron_right</i>
          </a>
          <div class="mdc-expansion-panel" id="ui-sub-pro">
            <nav class="mdc-list mdc-drawer-submenu">
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('programme.type')}}">
                  Planifier
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('programme.list')}}">
                  Liste
                </a>
              </div>
              {{-- <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="#">
                  Structure desactivées
                </a>
              </div> --}}
            </nav>
          </div>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-finance">
            <i class="fas fa-chart-line mdc-list-item__start-detail mdc-drawer-item-icon"></i>
            Bilan Financier
            <i class="mdc-drawer-arrow material-icons">chevron_right</i>
          </a>
          <div class="mdc-expansion-panel" id="ui-sub-finance">
            <nav class="mdc-list mdc-drawer-submenu">
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('admin.bilan_financier.index')}}">
                  Vue d'ensemble
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('admin.bilan_financier.historique')}}">
                  Historique 
                </a>
              </div>
            </nav>
          </div>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-client">
            <i class="fas fa-calendar-alt mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
            Type Client
            <i class="mdc-drawer-arrow material-icons">chevron_right</i>
          </a>
          <div class="mdc-expansion-panel" id="ui-sub-client">
            <nav class="mdc-list mdc-drawer-submenu">
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('client.all')}}">
                  Client
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('prospect.all')}}">
                  Prospect
                </a>
              </div>
              {{-- <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="#">
                  Structure desactivées
                </a>
              </div> --}}
            </nav>
          </div>
        </div>

        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-drawer-link" href="{{route('admin.demande.recuperation')}}">
            <i class="fas fa-box mdc-list-item__start-detail mdc-drawer-item-icon"></i>
            Récuperation demandé
          </a>
        </div>
    </div>
</aside>