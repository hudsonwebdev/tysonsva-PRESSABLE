const fs = require('fs');
const path = require('path');

/**
 * Webpack plugin to update the version number in functions.php
 * after CSS compilation completes.
 */
class VersionUpdatePlugin {
  constructor(options = {}) {
    this.functionsPath = options.functionsPath || path.resolve(process.cwd(), 'functions.php');
    this.versionFormat = options.versionFormat || 'timestamp'; // 'timestamp' or 'increment'
  }

  apply(compiler) {
    compiler.hooks.afterEmit.tapAsync('VersionUpdatePlugin', (compilation, callback) => {
      // Check if CSS was compiled (look for CSS files in the compilation)
      const hasCssOutput = Object.keys(compilation.assets).some(filename => 
        filename.endsWith('.css')
      );

      if (hasCssOutput) {
        this.updateVersion();
      }

      callback();
    });
  }

  updateVersion() {
    try {
      // Read the functions.php file
      let functionsContent = fs.readFileSync(this.functionsPath, 'utf8');

      // Extract current version
      const versionMatch = functionsContent.match(/define\s*\(\s*['"]_S_VERSION['"]\s*,\s*['"]([^'"]+)['"]\s*\)/);
      const currentVersion = versionMatch ? versionMatch[1] : null;

      // Generate new version based on format
      let newVersion;
      if (this.versionFormat === 'timestamp') {
        // Use timestamp for cache busting (format: YYYYMMDDHHMMSS)
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        newVersion = `${year}${month}${day}${hours}${minutes}${seconds}`;
      } else if (this.versionFormat === 'increment') {
        // Increment version intelligently
        if (currentVersion) {
          // Check if it's a semantic version (x.y.z)
          const semverMatch = currentVersion.match(/^(\d+)\.(\d+)\.(\d+)$/);
          if (semverMatch) {
            // Increment patch version (z)
            const major = parseInt(semverMatch[1], 10);
            const minor = parseInt(semverMatch[2], 10);
            const patch = parseInt(semverMatch[3], 10) + 1;
            newVersion = `${major}.${minor}.${patch}`;
          } else {
            // Try to parse as decimal number and increment
            const versionNum = parseFloat(currentVersion);
            if (!isNaN(versionNum)) {
              newVersion = (versionNum + 0.01).toFixed(2);
            } else {
              // Fallback to timestamp if version format is unknown
              const now = new Date();
              const year = now.getFullYear();
              const month = String(now.getMonth() + 1).padStart(2, '0');
              const day = String(now.getDate()).padStart(2, '0');
              const hours = String(now.getHours()).padStart(2, '0');
              const minutes = String(now.getMinutes()).padStart(2, '0');
              const seconds = String(now.getSeconds()).padStart(2, '0');
              newVersion = `${year}${month}${day}${hours}${minutes}${seconds}`;
            }
          }
        } else {
          // Default to 1.0.0 if version not found
          newVersion = '1.0.0';
        }
      } else {
        // Auto-detect: if current version is semantic, increment; otherwise use timestamp
        if (currentVersion && currentVersion.match(/^\d+\.\d+\.\d+$/)) {
          const semverMatch = currentVersion.match(/^(\d+)\.(\d+)\.(\d+)$/);
          const major = parseInt(semverMatch[1], 10);
          const minor = parseInt(semverMatch[2], 10);
          const patch = parseInt(semverMatch[3], 10) + 1;
          newVersion = `${major}.${minor}.${patch}`;
        } else {
          // Use timestamp for non-semantic versions
          const now = new Date();
          const year = now.getFullYear();
          const month = String(now.getMonth() + 1).padStart(2, '0');
          const day = String(now.getDate()).padStart(2, '0');
          const hours = String(now.getHours()).padStart(2, '0');
          const minutes = String(now.getMinutes()).padStart(2, '0');
          const seconds = String(now.getSeconds()).padStart(2, '0');
          newVersion = `${year}${month}${day}${hours}${minutes}${seconds}`;
        }
      }

      // Replace the version number in functions.php
      const versionRegex = /(define\s*\(\s*['"]_S_VERSION['"]\s*,\s*['"])([^'"]+)(['"]\s*\))/;
      const updatedContent = functionsContent.replace(
        versionRegex,
        `$1${newVersion}$3`
      );

      // Only write if content changed
      if (updatedContent !== functionsContent) {
        fs.writeFileSync(this.functionsPath, updatedContent, 'utf8');
        console.log(`\n✅ Updated _S_VERSION to ${newVersion} in functions.php\n`);
      }
    } catch (error) {
      console.error('Error updating version in functions.php:', error);
    }
  }
}

module.exports = VersionUpdatePlugin;

