<div id ="editSpace">
                @if ((Auth::check() && $space->user_id == Auth::user()->id) || (Auth::check() && Auth::user()->isAdmin(Auth::user())))
                    <button id="editSpace{{ $space->id }}" onclick="editSpace({{ $space->id }})"
                        class="button-space-comment">&#9998;
                        <span id="text-config"><i id="text-icon" class="pencil"></i></span>
                    </button>
                    <button id="cancelEditSpace{{ $space->id }}" onclick="cancelEditSpace({{ $space->id }})"
                        style="visibility:hidden;" class="button-space-comment">&#10761;
                        <span><i class="cross"></i> </span>
                    </button>
                @endif
</div>
