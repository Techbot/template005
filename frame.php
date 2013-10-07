<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
     <script type="text/javascript" src="http://lib.ivank.net/ivank.js"></script>
     <script type="text/javascript" src="Box2dWeb-2.1.a.3.min.js"></script>
     <script type="text/javascript">
          var world;
          var bodies = [];	// instances of b2Body (from Box2D)
          var actors = [];	// instances of Bitmap (from IvanK)
          var up;
          
          function Start() 
          {	
               var stage = new Stage("c");
               stage.addEventListener(Event.ENTER_FRAME, onEF);
               
               // background
               var bg = new Bitmap( new BitmapData("winter2.jpg") );
               bg.scaleX = bg.scaleY = stage.stageHeight/512;
               stage.addChild(bg);
               
               var	b2Vec2		= Box2D.Common.Math.b2Vec2,
                    	b2BodyDef	= Box2D.Dynamics.b2BodyDef,
                    	b2Body		= Box2D.Dynamics.b2Body,
                    	b2FixtureDef	= Box2D.Dynamics.b2FixtureDef,
                    	b2World		= Box2D.Dynamics.b2World,
                    	b2PolygonShape	= Box2D.Collision.Shapes.b2PolygonShape;
                    	b2CircleShape	= Box2D.Collision.Shapes.b2CircleShape;
               
               world = new b2World(new b2Vec2(0, 10),  true);
               up = new b2Vec2(0, -5);
               
               // I decided that 1 meter = 100 pixels
               
               var bxFixDef	= new b2FixtureDef();	// box  fixture definition
               bxFixDef.shape	= new b2PolygonShape();
               var blFixDef	= new b2FixtureDef();	// ball fixture definition
               blFixDef.shape	= new b2CircleShape();
               bxFixDef.density	= blFixDef.density = 1;
               
               var bodyDef = new b2BodyDef();
               bodyDef.type = b2Body.b2_staticBody;
               
               // create ground
               bxFixDef.shape.SetAsBox(10, 1);
               bodyDef.position.Set(9, stage.stageHeight/100 + 1);
               world.CreateBody(bodyDef).CreateFixture(bxFixDef);
               
               bxFixDef.shape.SetAsBox(1, 100);
               // left wall
               bodyDef.position.Set(-1, 3);
               world.CreateBody(bodyDef).CreateFixture(bxFixDef);
               // right wall
               bodyDef.position.Set(stage.stageWidth/100 + 1, 3);
               world.CreateBody(bodyDef).CreateFixture(bxFixDef);
               
               // both images are 200 x 200 px
               var bxBD = new BitmapData("box.jpg");
               var blBD = new BitmapData("bigball.png");
               
               // let's add 25 boxes and 25 balls!
               bodyDef.type = b2Body.b2_dynamicBody;
               for(var i = 0; i < 50; i++)
               {
                    var hw = 0.1 + Math.random()*0.45;	// "half width"
                    var hh = 0.1 + Math.random()*0.45;	// "half height"
                    
                    bxFixDef.shape.SetAsBox(hw, hh);
                    blFixDef.shape.SetRadius(hw);
                    bodyDef.position.Set(Math.random()*7, -5 + Math.random()*5);
                    
                    var body = world.CreateBody(bodyDef);
                    if(i<25) body.CreateFixture(bxFixDef);	// box
                    else     body.CreateFixture(blFixDef);	// ball
                    bodies.push(body);
                    
                    var bm = new Bitmap(i<25 ? bxBD : blBD);  bm.x = bm.y = -100;
                    var actor = new Sprite();  actor.addChild(bm);
                    if(i<25) { actor.scaleX = hw;  actor.scaleY = hh; }
                    else     { actor.scaleX = actor.scaleY = hw;      }
                    
                    actor.addEventListener(MouseEvent.MOUSE_MOVE, Jump);	
                    stage.addChild(actor);
                    actors.push(actor);
               }
          }
          
          function onEF(e) 
          {
               world.Step(1 / 60,  3,  3);
               world.ClearForces();
               
               for(var i=0; i<actors.length; i++)
               {
                    var body  = bodies[i];
                    var actor = actors [i];
                    var p = body.GetPosition();
                    actor.x = p.x *100;	// updating actor
                    actor.y = p.y *100;
                    actor.rotation = body.GetAngle()*180/Math.PI;
               }
          }
          
          function Jump(e)
          {
               var a = e.currentTarget;	// current actor
               var i = actors.indexOf(a);
               //  cursor might be over ball bitmap, but not over a real ball
               if(i>=25 && Math.sqrt(a.mouseX*a.mouseX + a.mouseY*a.mouseY) > 100) return;
               bodies[i].ApplyImpulse(up, bodies[i].GetWorldCenter());
          }
     </script>
</head>
<body onload="Start();"><canvas id="c"></canvas></body>
</html>
