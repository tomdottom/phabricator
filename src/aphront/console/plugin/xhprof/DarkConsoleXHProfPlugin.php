<?php

/*
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

class DarkConsoleXHProfPlugin extends DarkConsolePlugin {

  protected $xhprofID;

  public function getName() {
    $run = $this->getData();

    if ($run) {
      return '<span style="color: #ff00ff;">&bull;</span> XHProf';
    }

    return 'XHProf';
  }

  public function getDescription() {
    return 'Provides detailed PHP profiling information through XHProf.';
  }

  public function generateData() {
    return $this->xhprofID;
  }

  public function getXHProfRunID() {
    return $this->xhprofID;
  }

  public function render() {
    if (!DarkConsoleXHProfPluginAPI::isProfilerAvailable()) {
      return
        '<p>The "xhprof" PHP extension is not available. Install xhprof '.
        'to enable the XHProf plugin.';
    }

    $run = $this->getXHProfRunID();
    if ($run) {
      return '<a href="/xhprof/profile/'.$run.'/">View Run</a>';
    } else {
      $hidden = array();
      $data = array('__profile__' => 'page') + $_GET;

      foreach ($data as $k => $v) {
        $hidden[] = phutil_render_tag(
          'input',
          array(
            'type' => 'hidden',
            'name' => $k,
            'value' => $v,
          ));
      }
      $hidden = implode("\n", $hidden);


      return
        '<form method="get">'.
          $hidden.
          '<button>Enable XHProf</button>'.
        '</form>';
    }
  }


  public function willShutdown() {
    if (isset($_REQUEST['__profile__']) &&
        $_REQUEST['__profile__'] != 'all') {
      $this->xhprofID = DarkConsoleXHProfPluginAPI::stopProfiler();
    }
  }

}
/*

  public function render() {
    $run = $this->getData();

    if ($run) {
      $uri = 'http://www.intern.facebook.com/intern/phprof/?run='.$run;
      return
        <x:frag>
          <h1>XHProf Results</h1>
          <div class="XHProfPlugin">
            <a href={$uri} target="_blank" class="XHProfPlugin">Permalink</a>
            <iframe src={$uri} width="100%" height="600" />
          </div>
        </x:frag>;
    }

    $uri = URI::getRequestURI();
    return
      <x:frag>
        <h1>XHProf</h1>
        <form action={$uri} method="get" class="EnableFeature">
          <fieldset>
            <legend>Enable Profiling</legend>
            <p>Profiling was not enabled for this request. Click the button
            below to rerun the request with profiling enabled.</p>
            <button type="submit" name="_profile_" value="all"
                style="margin: 2px 1em; width: 75%;">
              Profile Page (With Includes)
            </button>
            <button type="submit" name="_profile_" value="exec"
                style="margin: 2px 1em; width: 75%;">
              Profile Page (No Includes)
            </button>
          </fieldset>
        </form>
      </x:frag>;
  }

  public function didShutdown() {
    if ($this->xhprofData) {
      require_module_lazy('profiling/phprof');
      $this->xhprofID = phprof_save_run($this->xhprofData);
    }
  }

}
*/
