<aside class="mdc-drawer mdc-drawer--dismissible mdc-drawer--open" style="background-color: red">
      <div class="mdc-drawer__header" >
        <a href="{{route('chauffeur.dashboard')}}" class="brand-logo">
          <img src="{{asset('assets/img/aft.jpg')}}" style="width: 50%; margin-left: 50px" alt="logo">
        </a>
      </div>
      <div class="mdc-drawer__content">
        <div class="user-info">
          <p class="name text-center text-black"> {{Auth::guard('chauffeur')->user()->name}} {{Auth::guard('chauffeur')->user()->prenom}} </p>
          <p class="email text-center text-black">{{Auth::guard('chauffeur')->user()->email}}</p>
        </div>
        <div class="mdc-list-group">
          <nav class="mdc-list mdc-drawer-menu">
            <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="{{route('chauffeur.dashboard')}}">
                <i class="fas fa-home mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                Tableau de bord
              </a>
            </div>
            <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="{{route('chauffeur.programme')}}">
                <i class="fas fa-calendar-alt mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                Mes programmes 
              </a>
            </div>
            <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="{{route('chauffeur.history')}}">
                <i class="fas fa-calendar mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                Historiques 
              </a>
            </div>
            {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="#">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">save</i>
                Demandes 
              </a>
            </div> --}}
            {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="{{route('chauffeur.permanent-personnel.create')}}">
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
            {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-perso">
                  <i class="fas fa-home-user mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                  Agence / chauffeur
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
                    <a class="mdc-drawer-link" href="{{route('chauffeur.create')}}">
                      chauffeur
                    </a>
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Structure desactivées
                    </a>
                  </div>
                </nav>
              </div>
            </div> --}}
            {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-chauffeur">
                 <i class="fas fa-file-invoice-dollar mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                Gestion Devis
                <i class="mdc-drawer-arrow material-icons">chevron_right</i>
              </a>
              <div class="mdc-expansion-panel" id="ui-sub-chauffeur">
                <nav class="mdc-list mdc-drawer-submenu">
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      En attente
                    </a>
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Confirmé/Validé
                    </a>
                  </div>
                </nav>
              </div>
            </div> --}}
            {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-colis">
                 <i class="fas fa-box mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                Gestion Colis
                <i class="mdc-drawer-arrow material-icons">chevron_right</i>
              </a>
              <div class="mdc-expansion-panel" id="ui-sub-colis">
                <nav class="mdc-list mdc-drawer-submenu">
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Ajouter
                    </a>
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Colis enregistré
                    </a>
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Historiques 
                    </a>
                  </div>
                </nav>
              </div>
            </div> --}}
            {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-conteneur">
                 <i class="fas fa-boxes mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                Gestion Conteneur
                <i class="mdc-drawer-arrow material-icons">chevron_right</i>
              </a>
              <div class="mdc-expansion-panel" id="ui-sub-conteneur">
                <nav class="mdc-list mdc-drawer-submenu">
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Ajouter 
                    </a>
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Listes
                    </a>
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Historiques 
                    </a>
                  </div>
                </nav>
              </div>
            </div> --}}
            {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-menu">
                 <i class="fas fa-qrcode mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                 Scanner
                <i class="mdc-drawer-arrow material-icons">chevron_right</i>
              </a>
              <div class="mdc-expansion-panel" id="ui-sub-menu">
                <nav class="mdc-list mdc-drawer-submenu">
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Mise en entrépot
                    </a>
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Chargement
                    </a>
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Déchargment
                    </a>
                  </div>
                </nav>
              </div>
            </div> --}}
            {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-code">
                <i class="fas fa-calendar-alt mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                Planning voyage
                <i class="mdc-drawer-arrow material-icons">chevron_right</i>
              </a>
              <div class="mdc-expansion-panel" id="ui-sub-code">
                <nav class="mdc-list mdc-drawer-submenu">
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Planifier 
                    </a>
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Liste
                    </a>
                  </div>
                </nav>
              </div>
            </div> --}}
             {{-- <div class="mdc-list-item mdc-drawer-item">
                  <a class="mdc-drawer-link" href="#">
                    <i class="fas fa-user-tie mdc-list-item__start-detail mdc-drawer-item-icon"></i>
                    Chauffeur
                  </a>
              </div> --}}
          </div>
    </aside>