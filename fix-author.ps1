# Script pour corriger l'auteur de tous les commits sur la branche emmanuel

Set-Location "C:\Users\emman\Downloads\ChallengeWeb-main"

Write-Host "Correction de l'auteur des commits..."

# Utiliser filter-branch pour changer l'auteur de tous les commits
git filter-branch --env-filter '
OLD_NAME="k-emmanuel"
CORRECT_NAME="K-emmanuel"
CORRECT_EMAIL="emmanuelmakosso00@gmail.com"

if [ "$GIT_COMMITTER_NAME" = "$OLD_NAME" ]
then
    export GIT_COMMITTER_NAME="$CORRECT_NAME"
    export GIT_COMMITTER_EMAIL="$CORRECT_EMAIL"
fi
if [ "$GIT_AUTHOR_NAME" = "$OLD_NAME" ]
then
    export GIT_AUTHOR_NAME="$CORRECT_NAME"
    export GIT_AUTHOR_EMAIL="$CORRECT_EMAIL"
fi
' --tag-name-filter cat -- main..emmanuel

Write-Host "`nAuteurs corrigés ! Vérification..."
git log emmanuel --format="%an <%ae>" -5
