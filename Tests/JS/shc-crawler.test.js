const path = require('path');

// Load the script after the DOM has been set up in each test.
function loadScript() {
  jest.resetModules();
  require(path.resolve(__dirname, '../../assets/js/shc-crawler.js'));
}

describe('shc-crawler script', () => {
  beforeEach(() => {
    // JSDOM environment by jest sets up document and window
    document.body.innerHTML = '';
    window.shcData = { endpoint: '/track', nonce: 'abc', debug: false };
    window.innerHeight = 800;
    window.innerWidth = 600;
    global.jQuery = { ajax: jest.fn() };
  });

  test('collectLinks returns only visible links', () => {
    document.body.innerHTML = `
      <a id="l1" href="https://example.com">Example</a>
      <a id="l2" href="https://visible.com">Visible</a>
      <a id="l3" href="https://hidden.com">Hidden</a>`;

    document.getElementById('l1').getBoundingClientRect = () => ({ top: 10 });
    document.getElementById('l2').getBoundingClientRect = () => ({ top: 200 });
    document.getElementById('l3').getBoundingClientRect = () => ({ top: 900 });

    loadScript();
    const { collectLinks } = window.shcCrawlerTest;
    const links = collectLinks();

    expect(links).toHaveLength(2);
    expect(links[0]).toMatchObject({ text: 'Example', position: 10 });
    expect(links[1]).toMatchObject({ text: 'Visible', position: 200 });
  });

  test('sendData uses jQuery.ajax with payload', () => {
    loadScript();
    const { sendData } = window.shcCrawlerTest;

    const data = { links: [{ url: 'u', text: 't', position: 1 }] };
    sendData('/track', data);

    expect(jQuery.ajax).toHaveBeenCalledTimes(1);
    const options = jQuery.ajax.mock.calls[0][0];
    expect(options.url).toBe('/track');
    expect(options.type).toBe('POST');
    expect(options.contentType).toBe('application/json');
    const parsed = JSON.parse(options.data);
    expect(parsed.links).toEqual(data.links);
    expect(parsed.width).toBe(600);
    expect(parsed.height).toBe(800);
    expect(parsed.nonce).toBe('abc');
    expect(parsed.timestamp).toBeDefined();
  });

  test('script sends data on DOMContentLoaded', () => {
    document.body.innerHTML = `<a href='https://x.com'>x</a>`;
    document.querySelector('a').getBoundingClientRect = () => ({ top: 5 });
    loadScript();

    document.dispatchEvent(new Event('DOMContentLoaded'));

    expect(jQuery.ajax).toHaveBeenCalled();
  });
});