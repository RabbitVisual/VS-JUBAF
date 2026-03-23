import fs from 'fs/promises';
import path from 'path';
import { pathToFileURL, fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

async function collectModuleAssetsPaths(initialPaths, modulesPath) {
  const absoluteModulesPath = path.join(__dirname, modulesPath);
  const moduleStatusesPath = path.join(__dirname, 'modules_statuses.json');

  // Start with root paths, using full path as key for transparency
  const finalPaths = {};
  for (const p of initialPaths) {
    finalPaths[p] = p;
  }

  try {
    // Read module_statuses.json
    const moduleStatusesContent = await fs.readFile(moduleStatusesPath, 'utf-8');
    const moduleStatuses = JSON.parse(moduleStatusesContent);

    // Read module directories
    const moduleDirectories = await fs.readdir(absoluteModulesPath);

    for (const moduleDir of moduleDirectories) {
      if (moduleDir === '.DS_Store' || moduleStatuses[moduleDir] !== true) continue;

      const viteConfigPath = path.join(absoluteModulesPath, moduleDir, 'vite.config.js');

      try {
        await fs.access(viteConfigPath);
        // Convert to a file URL for Windows compatibility
        const moduleConfigURL = pathToFileURL(viteConfigPath);

        // Import the module-specific Vite configuration
        const moduleConfig = await import(moduleConfigURL.href);

        if (moduleConfig.paths && Array.isArray(moduleConfig.paths)) {
          for (const p of moduleConfig.paths) {
            try {
              const absolutePath = path.join(__dirname, p);
              const stats = await fs.stat(absolutePath);
              if (stats.size > 10) { // Skip empty or near-empty files
                const normalizedPath = p.replaceAll(path.sep, '/');
                finalPaths[normalizedPath] = normalizedPath;
              }
            } catch (e) {
              // File might not exist or be unreadable
            }
          }
        }
      } catch (error) {
        // vite.config.js does not exist, skip this module
      }
    }
  } catch (error) {
    console.error(`Error: ${error}`);
  }

  return finalPaths;
}

export default collectModuleAssetsPaths;
