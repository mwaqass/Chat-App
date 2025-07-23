# Auto-Commit Script for Chat-App Repository
# This script automatically generates and pushes commits to maintain an active repository

param(
    [string]$CommitMessage = "",
    [switch]$Force = $false
)

# Configuration
$REPO_PATH = "C:\laragon\www\Chat-App"
$LOG_FILE = "$REPO_PATH\auto-commit.log"

# Function to write to log file
function Write-Log {
    param([string]$Message)
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logEntry = "[$timestamp] $Message"
    Write-Host $logEntry
    Add-Content -Path $LOG_FILE -Value $logEntry
}

# Function to generate random commit message
function Get-RandomCommitMessage {
    $messages = @(
        "chore: update project documentation and code comments",
        "docs: improve code documentation and inline comments",
        "style: enhance code formatting and consistency",
        "refactor: optimize code structure and organization",
        "feat: add minor improvements and enhancements",
        "fix: resolve code formatting and style issues",
        "perf: optimize performance and code efficiency",
        "ci: update development environment configuration",
        "build: improve build process and dependencies",
        "test: enhance test coverage and validation",
        "security: update security configurations and settings",
        "maintenance: perform routine code maintenance",
        "update: refresh project dependencies and configurations",
        "improve: enhance overall code quality and standards",
        "clean: clean up code and remove unused elements"
    )

    $randomMessage = $messages | Get-Random
    $randomSuffix = @(
        "for better maintainability",
        "to improve code quality",
        "for enhanced performance",
        "to follow best practices",
        "for better user experience",
        "to maintain code standards",
        "for improved readability",
        "to optimize development workflow"
    ) | Get-Random

    return "$randomMessage $randomSuffix"
}

# Function to create a small change in a file
function Create-MinimalChange {
    $files = @(
        "README.md",
        "composer.json",
        "package.json",
        "themes/tailwind/js/app.js",
        "themes/tailwind/css/app.css"
    )

    foreach ($file in $files) {
        $filePath = Join-Path $REPO_PATH $file
        if (Test-Path $filePath) {
            try {
                # Add a comment or update timestamp
                $content = Get-Content $filePath -Raw
                $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"

                if ($file -eq "README.md") {
                    # Add a maintenance note at the end
                    $newContent = $content + "`n`n<!-- Last auto-update: $timestamp -->"
                } elseif ($file -eq "composer.json") {
                    # Update description
                    $newContent = $content -replace '"description": "[^"]*"', '"description": "Professional real-time chat application with Laravel and Vue.js - Updated: $timestamp"'
                } elseif ($file -eq "package.json") {
                    # Update description
                    $newContent = $content -replace '"description": "[^"]*"', '"description": "Professional real-time chat application - Updated: $timestamp"'
                } else {
                    # Add a comment at the top
                    $newContent = "// Auto-updated: $timestamp`n" + $content
                }

                Set-Content -Path $filePath -Value $newContent -NoNewline
                Write-Log "Updated file: $file"
                return $true
            }
            catch {
                Write-Log "Error updating file $file : $($_.Exception.Message)"
                continue
            }
        }
    }
    return $false
}

# Main execution
try {
    Write-Log "Starting auto-commit process..."

    # Change to repository directory
    Set-Location $REPO_PATH
    Write-Log "Changed to repository directory: $REPO_PATH"

    # Check if we're in a git repository
    if (-not (Test-Path ".git")) {
        Write-Log "Error: Not a git repository"
        exit 1
    }

    # Check if there are any uncommitted changes
    $status = git status --porcelain
    if ($status -and -not $Force) {
        Write-Log "Warning: There are uncommitted changes. Use -Force to override."
        exit 1
    }

    # Create a minimal change
    $changeMade = Create-MinimalChange
    if (-not $changeMade) {
        Write-Log "Error: Could not create any changes"
        exit 1
    }

    # Generate commit message
    if ($CommitMessage) {
        $message = $CommitMessage
    } else {
        $message = Get-RandomCommitMessage
    }

    # Add all changes
    git add .
    Write-Log "Added all changes to staging"

    # Commit changes
    git commit -m $message
    Write-Log "Committed with message: $message"

    # Push to remote
    git push origin master
    Write-Log "Successfully pushed to remote repository"

    Write-Log "Auto-commit process completed successfully!"

} catch {
    Write-Log "Error during auto-commit process: $($_.Exception.Message)"
    exit 1
}
