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
    navigator.sendBeacon = jest.fn().mockReturnValue(true);
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

  test('sendData uses sendBeacon with payload', () => {
    loadScript();
    const { sendData } = window.shcCrawlerTest;

    const data = { links: [{ url: 'u', text: 't', position: 1 }] };
    sendData('/track', data);

    expect(navigator.sendBeacon).toHaveBeenCalledTimes(1);
    const [url, payload] = navigator.sendBeacon.mock.calls[0];
    expect(url).toBe('/track');
    const parsed = JSON.parse(payload);
    expect(parsed.links).toEqual(data.links);
    expect(parsed.width).toBe(600);
    expect(parsed.height).toBe(800);
    expect(parsed.timestamp).toBeDefined();
  });

  test('script sends data on DOMContentLoaded', () => {
    document.body.innerHTML = `<a href='https://x.com'>x</a>`;
    document.querySelector('a').getBoundingClientRect = () => ({ top: 5 });
    loadScript();

    document.dispatchEvent(new Event('DOMContentLoaded'));

    expect(navigator.sendBeacon).toHaveBeenCalled();
  });
});