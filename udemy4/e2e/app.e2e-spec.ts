import { Udemy4Page } from './app.po';

describe('udemy4 App', () => {
  let page: Udemy4Page;

  beforeEach(() => {
    page = new Udemy4Page();
  });

  it('should display welcome message', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('Welcome to app!');
  });
});
