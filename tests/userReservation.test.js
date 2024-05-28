// tests/example.test.js
QUnit.module('Example Module', function() {
    QUnit.test('sum function', function(assert) {
      function sum(a, b) {
        return a + b;
      }
      assert.equal(sum(1, 2), 3, '1 + 2 should equal 3');
    });
  });